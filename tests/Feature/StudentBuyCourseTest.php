<?php

namespace Tests\Feature;

use App\Models\Erp\Payment;
use App\Models\Erp\Student;
use App\Models\StudentAccount;
use App\Models\StudentCourseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// "Buy a Course" from the student profile → admin approval → enrolled. Rolled back after each test.
class StudentBuyCourseTest extends TestCase
{
    use DatabaseTransactions;

    private StudentAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
        $this->account = StudentAccount::create(['name' => 'Buyer Student', 'email' => 'buyer-student@example.com', 'phone' => '01712340551', 'phone_key' => '01712340551', 'password' => 'secret123'])->refresh();
        $this->account->approve(); // ERP student without a course, like a new website student
    }

    private function admin(): User
    {
        return User::where('admin', 1)->where('status', 1)->firstOrFail();
    }

    private function course($i = 0)
    {
        return trainingCourses()->values()[$i];
    }

    private function buy(array $data = [])
    {
        return $this->actingAs($this->account, 'student')->from('/student/courses/buy')->post('/student/courses/buy', $data + [
            'course_id' => $this->course()->id, 'note' => 'Evening batch please',
        ]);
    }

    public function test_profile_has_buy_button_and_buy_page_lists_courses(): void
    {
        $this->actingAs($this->account, 'student')->get('/student/profile')->assertOk()->assertSee('Buy a Course')->assertSee(route('student.courses.buy'));
        $this->get('/student/courses/buy')->assertOk()->assertSee($this->course()->course('title'))->assertSee('Send Course Request');
    }

    public function test_request_waits_then_admin_approval_adds_the_course(): void
    {
        $this->buy()->assertRedirect('/student/profile')->assertSessionHas('success');
        $request = StudentCourseRequest::where('account_id', $this->account->id)->firstOrFail();
        $this->assertSame('pending', $request->status);
        $this->assertSame('Evening batch please', $request->note);
        $this->assertSame(0, $this->account->enrollments()->whereNotNull('course_id')->count());
        $this->get('/student/profile')->assertSee('Waiting for approval')->assertSee($this->course()->course('title'));
        // The same course cannot be asked for twice, and is no longer offered
        $this->buy()->assertSessionHasErrors('course_id');
        $this->get('/student/courses/buy')->assertDontSee('value="'.$this->course()->id.'"', false);

        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin')->assertSee(route('admin.courseRequests'));
        $this->actingAs($admin)->get('/admin/website/course-requests')->assertOk()->assertSee('Buyer Student')->assertSee('Evening batch please');
        $this->actingAs($admin)->from('/admin/website/course-requests')->post('/admin/website/course-requests/approve/'.$request->id)->assertSessionHas('success');

        $request->refresh();
        $this->assertSame('approved', $request->status);
        $records = $this->account->enrollments()->get();
        $this->assertCount(1, $records, 'the empty record from the registration is filled, not a second one');
        $record = $records->first();
        $this->assertSame((int) $this->course()->id, (int) $record->course_id);
        $this->assertSame($request->student_id, $record->id);
        $this->assertSame(0, Payment::where('student_id', $record->id)->count(), 'no payment is made by a request');
        $this->assertEquals(\App\Support\Erp::courseFee($this->course()), $record->fee);

        // Approving again does nothing more
        $this->actingAs($admin)->post('/admin/website/course-requests/approve/'.$request->id)->assertSessionHas('error');
        $this->assertSame(1, $this->account->enrollments()->count());

        $this->actingAs($this->account, 'student')->get('/student/profile')->assertSee('Approved · Enrolled')->assertSee($record->code);
    }

    public function test_second_course_stays_under_the_same_student_id(): void
    {
        $this->buy();
        $this->buy(['course_id' => $this->course(1)->id, 'note' => '']);
        $admin = $this->admin();
        foreach (StudentCourseRequest::where('account_id', $this->account->id)->get() as $request) {
            $this->actingAs($admin)->post('/admin/website/course-requests/approve/'.$request->id);
        }
        $records = $this->account->enrollments()->get();
        $this->assertCount(2, $records);
        $this->assertSame(1, $records->pluck('student_no')->unique()->count());
    }

    public function test_reject_with_reason_and_cancel(): void
    {
        $this->buy();
        $request = StudentCourseRequest::where('account_id', $this->account->id)->firstOrFail();
        $this->actingAs($this->admin())->post('/admin/website/course-requests/reject/'.$request->id, ['admin_note' => 'Payment not found'])->assertSessionHas('success');
        $this->assertSame('rejected', $request->fresh()->status);
        $this->assertSame(0, $this->account->enrollments()->whereNotNull('course_id')->count());
        $this->actingAs($this->account, 'student')->get('/student/profile')->assertSee('Not approved')->assertSee('Payment not found');

        // After a rejection the course can be asked for again, and a waiting request can be cancelled
        $this->buy()->assertRedirect('/student/profile');
        $again = StudentCourseRequest::where('account_id', $this->account->id)->where('status', 'pending')->firstOrFail();
        $this->post('/student/courses/requests/'.$again->id.'/cancel')->assertRedirect('/student/profile');
        $this->assertNull(StudentCourseRequest::find($again->id));
    }

    public function test_bad_input_and_other_students_are_refused(): void
    {
        $this->buy(['course_id' => 999999])->assertSessionHasErrors('course_id');
        $this->buy(['course_id' => ''])->assertSessionHasErrors('course_id');
        $this->buy(['note' => str_repeat('x', 501)])->assertSessionHasErrors('note');
        $this->assertSame(0, StudentCourseRequest::where('account_id', $this->account->id)->count());

        // A waiting account cannot buy; another student cannot cancel this one's request
        $waiting = StudentAccount::create(['name' => 'Waiting Buyer', 'email' => 'waiting-buyer@example.com', 'phone' => '01712340552', 'phone_key' => '01712340552', 'password' => 'secret123'])->refresh();
        $this->actingAs($waiting, 'student')->get('/student/courses/buy')->assertRedirect('/student/profile');
        $this->actingAs($waiting, 'student')->post('/student/courses/buy', ['course_id' => $this->course()->id])->assertRedirect('/student/profile');
        $this->buy();
        $mine = StudentCourseRequest::where('account_id', $this->account->id)->firstOrFail();
        $this->actingAs($waiting, 'student')->post('/student/courses/requests/'.$mine->id.'/cancel')->assertNotFound();
        // Students cannot approve
        $this->post('/admin/website/course-requests/approve/'.$mine->id);
        $this->assertSame('pending', $mine->fresh()->status);
    }
}
