<?php

namespace Tests\Feature;

use App\Models\Erp\Student;
use App\Models\Permission;
use App\Models\StudentAccount;
use App\Models\User;
use App\Support\Erp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// One student in several courses (ERP → Students → Add Course). Runs inside a transaction that is rolled back.
class ErpMultiCourseTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
        $role = Permission::where('permission', 'like', '%appsSetting%')->first();
        $user = new User(['name' => 'Multi Course Admin', 'email' => 'multi-course-admin@example.com', 'password' => Hash::make('secret-pass')]);
        $user->admin = 1;
        $user->status = 1;
        $user->permission_id = $role?->id;
        $user->save();
        $this->actingAs($user);
    }

    public function test_students_page_has_the_add_course_button(): void
    {
        Student::create(['name' => 'Button Student', 'phone' => '01799900011', 'course_id' => Erp::courses()->first()->id, 'fee' => 1000]);
        $this->get('/admin/students')->assertOk()->assertSee('Add Course')->assertSee('addStudentCourse(', false);
    }

    public function test_add_course_enrolls_the_same_student_in_another_course(): void
    {
        [$plc, $vfd] = Erp::courses()->values()->take(2)->all();
        $account = tap(StudentAccount::create(['name' => 'Multi Student', 'email' => 'multi-student@example.com', 'phone' => '01799900022', 'phone_key' => '01799900022', 'password' => 'secret123']), fn ($a) => $a->forceFill(['approved_at' => now()])->save());
        $first = Student::create(['account_id' => $account->id, 'name' => 'Multi Student', 'phone' => '01799900022', 'course_id' => $plc->id, 'fee' => 1000]);

        $this->post('/admin/records/student', ['name' => 'Multi Student', 'phone' => '01799900022', 'course' => $vfd->id, 'batch' => '', 'from' => $first->id])
            ->assertSessionHas('toast', 'Record saved successfully');
        $second = Student::where('course_id', $vfd->id)->where('account_id', $account->id)->firstOrFail();
        $this->assertNotSame($first->id, $second->id);
        $this->assertEquals(Erp::courseFee($vfd), $second->fee);

        // Both courses on the student's profile
        $this->actingAs($account->refresh(), 'student');
        $this->get('/student/profile')->assertSee($plc->course('title'))->assertSee($vfd->course('title'))->assertSee('2 courses');
    }

    public function test_the_same_course_twice_is_refused(): void
    {
        $plc = Erp::courses()->first();
        $first = Student::create(['name' => 'Twice Student', 'phone' => '01799900033', 'course_id' => $plc->id, 'fee' => 1000]);

        $this->post('/admin/records/student', ['name' => 'Twice Student', 'phone' => '01799900033', 'course' => $plc->id, 'batch' => '', 'from' => $first->id])
            ->assertSessionHas('toast', 'This student is already enrolled in that course ('.$first->code.')');
        // Also when entered again by hand with the mobile written differently
        $this->post('/admin/records/student', ['name' => 'Twice Student', 'phone' => '+8801799900033', 'course' => $plc->id, 'batch' => '']);
        $this->assertSame(1, Student::where('course_id', $plc->id)->where('phone', 'like', '%1799900033')->count());
    }

    public function test_a_student_without_a_course_gets_it_on_the_same_record(): void
    {
        $plc = Erp::courses()->first();
        $record = Student::create(['name' => 'Website Only', 'phone' => '01799900044', 'course_id' => null, 'fee' => 0]);
        $this->post('/admin/records/student/'.$record->id, ['name' => 'Website Only', 'phone' => '01799900044', 'course' => $plc->id, 'batch' => ''])
            ->assertSessionHas('toast', 'Record saved successfully');
        $this->assertSame($plc->id, $record->fresh()->course_id);
        $this->assertSame(1, Student::where('phone', '01799900044')->count());
    }

    public function test_another_course_keeps_the_same_student_id(): void
    {
        [$plc, $vfd, $scada] = Erp::courses()->values()->take(3)->all();
        $first = Student::create(['name' => 'Same Id Student', 'phone' => '01799900066', 'course_id' => $plc->id, 'fee' => 1000]);

        // With "Add Course", and with "Add Student" using the same mobile number written differently
        $this->post('/admin/records/student', ['name' => 'Same Id Student', 'phone' => '01799900066', 'course' => $vfd->id, 'batch' => '', 'from' => $first->id]);
        $this->post('/admin/records/student', ['name' => 'Same Id Student', 'phone' => '+880 1799-900066', 'course' => $scada->id, 'batch' => '']);

        $records = Student::where('student_no', $first->student_no)->get();
        $this->assertCount(3, $records);
        $this->assertSame([$first->code], $records->map->code->unique()->values()->all());

        // One student on the dashboard, every course listed under one ID
        $html = $this->get('/admin/students')->assertSee('Another course of the same student')->getContent();
        // Name, mobile and Student ID only on the first course row
        $this->assertSame(1, substr_count($html, '<td>Same Id Student</td>'));
        $this->assertSame(1, substr_count($html, '<td>01799900066</td>'));
        $this->assertSame(1, substr_count($html, '<td>'.$first->code.'</td>'));
        $this->assertStringContainsString(e($scada->course('title')), $html);
        $this->get('/find-certificate?number='.$first->code)->assertOk();
    }

    public function test_website_student_without_course_gets_it_on_the_same_id(): void
    {
        $plc = Erp::courses()->first();
        $account = tap(StudentAccount::create(['name' => 'Web Same Id', 'email' => 'web-same-id@example.com', 'phone' => '01799900077', 'phone_key' => '01799900077', 'password' => 'secret123']), fn ($a) => $a->forceFill(['approved_at' => now()])->save());
        $placeholder = Student::create(['account_id' => $account->id, 'name' => 'Web Same Id', 'phone' => '01799900077', 'course_id' => null, 'fee' => 0]);

        // Admin admits the same mobile number: the website record gets the course, no second record or ID
        $this->post('/admin/records/student', ['name' => 'Web Same Id', 'phone' => '01799900077', 'course' => $plc->id, 'batch' => '']);
        $this->assertSame(1, Student::where('account_id', $account->id)->count());
        $this->assertSame($plc->id, $placeholder->fresh()->course_id);
    }

    public function test_linking_an_office_student_merges_the_website_record(): void
    {
        $plc = Erp::courses()->first();
        $office = Student::create(['name' => 'Office Student', 'phone' => '01799900088', 'course_id' => $plc->id, 'fee' => 1000]);
        $account = tap(StudentAccount::create(['name' => 'Office Student', 'email' => 'office-student@example.com', 'phone' => '01799900088', 'phone_key' => '01799900088', 'password' => 'secret123']), fn ($a) => $a->forceFill(['approved_at' => now()])->save());
        $placeholder = Student::create(['account_id' => $account->id, 'name' => 'Office Student', 'phone' => '01799900088', 'course_id' => null, 'fee' => 0]);

        $this->post('/admin/website/student-accounts/link/'.$account->id, ['student' => $office->id]);
        $this->assertSame($account->id, $office->fresh()->account_id);
        $this->assertNull($placeholder->fresh());
        $this->assertSame([$office->code], Student::where('account_id', $account->id)->get()->map->code->unique()->values()->all());
    }

    public function test_fees_list_and_receipt_show_the_due(): void
    {
        $plc = Erp::courses()->first();
        $student = Student::create(['name' => 'Due Fee Student', 'phone' => '01799900099', 'course_id' => $plc->id, 'fee' => 12000]);
        \App\Models\Erp\Payment::create(['student_id' => $student->id, 'amount' => 3000, 'date' => '2026-11-01', 'method' => 'Cash']);
        \App\Models\Erp\Payment::create(['student_id' => $student->id, 'amount' => 4000, 'date' => '2026-11-05', 'method' => 'bKash']);

        // A second course of the same student: 8,000 more due
        Student::create(['student_no' => $student->student_no, 'name' => 'Due Fee Student', 'phone' => '01799900099', 'course_id' => Erp::courses()->values()->get(1)->id, 'fee' => 8000]);

        // Total due of the student in the list and on every receipt: (12,000 - 7,000) + 8,000
        $html = $this->get('/admin/fees')->assertOk()->assertSee('Total Due')->assertSee($student->code)->getContent();
        $this->assertStringContainsString('color:#d22d4b">'.Erp::fmt(13000), $html);
        $this->assertSame(2, substr_count($html, 'data-due="'.e(Erp::fmt(13000)).'"'));
    }

    public function test_student_lists_show_each_student_once_and_payment_goes_to_the_chosen_course(): void
    {
        [$plc, $vfd] = Erp::courses()->values()->take(2)->all();
        $first = Student::create(['name' => 'Once Listed', 'phone' => '01799900111', 'course_id' => $plc->id, 'fee' => 12000]);
        $second = Student::create(['student_no' => $first->student_no, 'name' => 'Once Listed', 'phone' => '01799900111', 'course_id' => $vfd->id, 'fee' => 8000]);

        // One option "name — Student ID", no course in it; the payment form has its own Course list
        $html = $this->get('/admin/fees')->getContent();
        preg_match('#<template id="tpl-payment">(.*?)</template>#s', $html, $payment);
        $this->assertSame(1, substr_count($payment[1], 'Once Listed — '.$first->code.'</option>'));
        $this->assertStringNotContainsString('Once Listed — '.$first->code.' — ', $html);
        $this->assertStringContainsString('data-cert-course', $payment[1]);

        $this->post('/admin/records/payment', ['student' => $first->id, 'course' => $vfd->id, 'amount' => 2000, 'date' => '2026-11-09', 'method' => 'Cash'])
            ->assertSessionHas('toast', 'Record saved successfully');
        $this->assertSame(2000.0, (float) $second->payments()->sum('amount'));
        $this->assertSame(0.0, (float) $first->payments()->sum('amount'));

        // Edit of that payment selects the student's entry and the course
        $feesHtml = $this->get('/admin/fees')->getContent();
        $this->assertStringContainsString('&quot;student&quot;:'.$first->id.',&quot;course&quot;:'.$vfd->id, $feesHtml);

        $other = Erp::courses()->values()->get(2);
        $this->post('/admin/records/payment', ['student' => $first->id, 'course' => $other->id, 'amount' => 100, 'date' => '2026-11-09', 'method' => 'Cash'])
            ->assertSessionHas('toast', 'This student is not enrolled in that course');
    }

    public function test_editing_a_record_keeps_its_own_course(): void
    {
        $plc = Erp::courses()->first();
        $record = Student::create(['name' => 'Edit Keep', 'phone' => '01799900055', 'course_id' => $plc->id, 'fee' => 1000]);
        $this->post('/admin/records/student/'.$record->id, ['name' => 'Edit Keep Renamed', 'phone' => '01799900055', 'course' => $plc->id, 'batch' => ''])
            ->assertSessionHas('toast', 'Record saved successfully');
        $this->assertSame('Edit Keep Renamed', $record->fresh()->name);
    }
}
