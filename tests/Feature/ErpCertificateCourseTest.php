<?php

namespace Tests\Feature;

use App\Models\Erp\Certificate;
use App\Models\Erp\Student;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// ERP certificates: only for a course the student is enrolled in. Runs inside a transaction that is rolled back.
class ErpCertificateCourseTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
        $role = Permission::where('permission', 'like', '%appsSetting%')->first();
        $user = new User(['name' => 'Cert Test Admin', 'email' => 'cert-test-admin@example.com', 'password' => Hash::make('secret-pass')]);
        $user->admin = 1;
        $user->status = 1;
        $user->permission_id = $role?->id;
        $user->save();
        $this->actingAs($user);
    }

    private function issue($student, $course)
    {
        return $this->post('/admin/records/certificate', ['student' => $student, 'course' => $course, 'issued' => '2026-11-09', 'grade' => 'A']);
    }

    public function test_certificate_only_for_an_enrolled_course(): void
    {
        [$plc, $vfd] = \App\Support\Erp::courses()->values()->take(2)->all();
        $student = Student::create(['name' => 'Cert Course Student', 'phone' => '01766600011', 'course_id' => $plc->id, 'fee' => 1000]);

        $this->issue($student->id, $vfd->id)->assertSessionHas('toast', 'This student is not enrolled in that course');
        $this->assertSame(0, Certificate::where('student_id', $student->id)->count());

        $this->issue($student->id, $plc->id)->assertSessionHas('toast', 'Record saved successfully');
        $this->assertSame($plc->id, Certificate::where('student_id', $student->id)->firstOrFail()->course_id);
    }

    public function test_a_person_with_two_courses_gets_the_certificate_on_the_right_record(): void
    {
        [$plc, $vfd] = \App\Support\Erp::courses()->values()->take(2)->all();
        $first = Student::create(['name' => 'Two Course Student', 'phone' => '01766600022', 'course_id' => $plc->id, 'fee' => 1000]);
        $second = Student::create(['student_no' => $first->student_no, 'name' => 'Two Course Student', 'phone' => '+8801766600022', 'course_id' => $vfd->id, 'fee' => 1000]);

        // Chosen under the first record, for the course of the second one
        $this->issue($first->id, $vfd->id)->assertSessionHas('toast', 'Record saved successfully');
        $this->assertSame($second->id, Certificate::where('course_id', $vfd->id)->latest('id')->firstOrFail()->student_id);
    }

    public function test_the_form_knows_each_students_courses(): void
    {
        $course = \App\Support\Erp::courses()->first();
        $student = Student::create(['name' => 'Form Course Student', 'phone' => '01766600033', 'course_id' => $course->id, 'fee' => 1000]);
        $none = Student::create(['name' => 'No Course Student', 'phone' => '01766600044', 'course_id' => null, 'fee' => 0]);

        $html = $this->get('/admin/certificates')->assertOk()->assertSee('data-cert-course', false)->getContent();
        preg_match('/window\.CERT_COURSES = (\{.*?\});/s', $html, $m);
        $map = json_decode($m[1], true);
        $this->assertSame([$course->id], $map[$student->id]);
        $this->assertSame([], $map[$none->id]);
    }
}
