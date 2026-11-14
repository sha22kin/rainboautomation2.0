<?php

namespace Tests\Feature;

use App\Models\Erp\Student;
use App\Models\Permission;
use App\Models\StudentAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// Website registration adds the student to the ERP; ERP courses and certificates reach the student's profile.
// Runs against the configured database inside a transaction that is rolled back.
class StudentErpLinkTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
    }

    // Registers, then the admin approves (ERP → Registered Students) unless $approve is false
    private function register(array $data = [], $approve = true)
    {
        $response = $this->post('/student/register', $data + [
            'name' => 'Erp Link Student', 'email' => 'erp-link@example.com', 'phone' => '01755500011',
            'password' => 'secret123', 'password_confirmation' => 'secret123', 'agree' => '1',
            'profession' => 'Student', 'preferred_course_id' => trainingCourses()->first()->id,
        ]);
        if($approve && ($account = StudentAccount::where('email', strtolower(trim($data['email'] ?? 'erp-link@example.com')))->whereNull('approved_at')->first())){
            $account->approve();
        }
        return $response;
    }

    private function account(): StudentAccount
    {
        return StudentAccount::where('email', 'erp-link@example.com')->firstOrFail();
    }

    private function admin(): User
    {
        $role = Permission::where('permission', 'like', '%appsSetting%')->first();
        $user = new User(['name' => 'Erp Link Admin', 'email' => 'erp-link-admin@example.com', 'password' => Hash::make('secret-pass')]);
        $user->admin = 1;
        $user->status = 1;
        $user->permission_id = $role?->id;
        $user->save();
        return $user;
    }

    public function test_register_page_has_no_course_choice(): void
    {
        $this->get('/student/register')->assertOk()->assertDontSee('name="course"', false)->assertSee('name="preferred_course_id"', false)->assertDontSee('I will choose a course later');
    }

    public function test_registering_without_a_course_still_adds_the_student_to_the_erp(): void
    {
        $this->register()->assertRedirect('/student/login');
        $record = Student::where('account_id', $this->account()->id)->firstOrFail();
        $this->assertNull($record->course_id);

        // Listed in ERP → Students with the Website mark; no course card on the profile yet
        $this->actingAs($this->admin());
        $this->get('/admin/students')->assertOk()->assertSee('Erp Link Student')->assertSee('Has a website student account');
        $this->actingAs($this->account(), 'student');
        $this->get('/student/profile')->assertSee('No course is linked');
    }

    public function test_a_course_sent_with_the_registration_is_ignored(): void
    {
        $this->register(['course' => trainingCourses()->first()->id])->assertRedirect('/student/login');
        $this->assertNull(Student::where('account_id', $this->account()->id)->firstOrFail()->course_id);
    }

    public function test_existing_office_records_are_left_for_the_admin_to_confirm(): void
    {
        $old = Student::create(['name' => 'Erp Link Student', 'phone' => '01755-500011', 'course_id' => trainingCourses()->first()->id, 'fee' => 5000]);
        $this->register();
        // The website student still gets its own ERP record; the office's record waits for the admin's Link
        $this->assertNull($old->fresh()->account_id);
        $this->assertSame(1, Student::where('account_id', $this->account()->id)->whereNull('course_id')->count());
        $this->assertTrue($this->account()->suggestedEnrollments()->contains('id', $old->id));
    }

    public function test_course_added_in_the_erp_and_its_certificate_reach_the_profile(): void
    {
        $course = trainingCourses()->first();
        $this->register();

        // Admin admits the same mobile number to a course and later issues the certificate
        $this->actingAs($this->admin());
        $this->post('/admin/records/student', ['name' => 'Erp Link Student', 'phone' => '+8801755500011', 'course' => $course->id, 'batch' => ''])->assertSessionHas('toast', 'Record saved successfully');
        $record = Student::where('course_id', $course->id)->where('phone', '+8801755500011')->firstOrFail();
        $this->assertSame($this->account()->id, $record->account_id);

        $this->post('/admin/records/certificate', ['student' => $record->id, 'course' => $course->id, 'issued' => '2026-11-09', 'grade' => 'A'])->assertSessionHas('toast', 'Record saved successfully');
        $certificate = \App\Models\Erp\Certificate::where('student_id', $record->id)->firstOrFail();

        $this->actingAs($this->account(), 'student');
        $this->get('/student/profile')->assertSee($course->course('title'))->assertSee($certificate->code)->assertSee('Download');
        $this->get('/student/certificates/'.$certificate->id)->assertOk()->assertSee('Download PDF')->assertSee('Erp Link Student');
        $this->get('/student/certificates/'.$certificate->id.'?download=1')->assertOk();
    }

    public function test_an_unrelated_mobile_number_is_not_linked(): void
    {
        $this->register();
        $this->actingAs($this->admin());
        $this->post('/admin/records/student', ['name' => 'Someone Else', 'phone' => '01899900022', 'course' => trainingCourses()->first()->id, 'batch' => '']);
        $this->assertNull(Student::where('phone', '01899900022')->firstOrFail()->account_id);
    }
}
