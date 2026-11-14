<?php

namespace Tests\Feature;

use App\Models\Erp\Student;
use App\Models\StudentAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// Website registrations wait for the admin: no login and no ERP student until approved. Rolled back after each test.
class StudentApprovalTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
    }

    private function register(): StudentAccount
    {
        $this->post('/student/register', [
            'name' => 'Approval Student', 'email' => 'approval-student@example.com', 'phone' => '01712340661',
            'password' => 'secret123', 'password_confirmation' => 'secret123', 'agree' => '1',
            'profession' => 'Student', 'preferred_course_id' => trainingCourses()->first()->id,
        ])->assertRedirect('/student/login')->assertSessionHas('success', 'Registration successful! Please log in. Your account will be ready once Rainbow Automation approves your registration.');
        return StudentAccount::where('email', 'approval-student@example.com')->firstOrFail();
    }

    private function admin(): User
    {
        return User::where('admin', 1)->where('status', 1)->firstOrFail();
    }

    public function test_new_registration_logs_in_to_a_please_wait_profile(): void
    {
        $account = $this->register();
        $this->assertNull($account->approved_at);
        $this->assertSame(0, Student::where('account_id', $account->id)->count());

        $this->post('/student/login', ['email' => 'approval-student@example.com', 'password' => 'secret123'])->assertRedirect('/student/profile');
        $this->get('/student/profile')->assertOk()->assertSee('Please wait, your registration is under review')
            ->assertDontSee('My Information')->assertDontSee('Edit Profile');
        // Nothing else opens before approval
        $this->get('/student/profile/edit')->assertRedirect('/student/profile');
        $this->post('/student/profile/edit', ['name' => 'Changed Name', 'email' => 'approval-student@example.com', 'phone' => '01712340661'])->assertRedirect('/student/profile');
        $this->assertSame('Approval Student', $account->fresh()->name);
        $this->post('/student/profile/password', ['current_password' => 'secret123', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])->assertRedirect('/student/profile');
        $this->assertSame(0, Student::where('account_id', $account->id)->count());
    }

    public function test_admin_sees_waiting_registrations_and_approves(): void
    {
        $account = $this->register();
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Registered Students')->assertSee(route('admin.studentAccounts'));
        $this->actingAs($admin)->get('/admin/website/student-accounts?filter=pending')->assertOk()
            ->assertSee('Approval Student')->assertSee('Waiting for Approval')->assertSee(route('admin.studentAccountsAction', ['approve', $account->id]));

        $this->actingAs($admin)->from('/admin/website/student-accounts')->post('/admin/website/student-accounts/approve/'.$account->id)
            ->assertRedirect('/admin/website/student-accounts')->assertSessionHas('success');
        $this->assertNotNull($account->fresh()->approved_at);
        $this->assertSame(1, Student::where('account_id', $account->id)->count());

        // Approving again does not make a second ERP student
        $this->actingAs($admin)->post('/admin/website/student-accounts/approve/'.$account->id);
        $this->assertSame(1, Student::where('account_id', $account->id)->count());

        auth()->logout();
        $this->post('/student/login', ['email' => 'approval-student@example.com', 'password' => 'secret123'])->assertRedirect('/student/profile');
        $this->get('/student/profile')->assertOk()->assertSee('My Information')->assertSee('Approval Student')->assertDontSee('Please wait, your registration is under review');
    }

    public function test_admin_student_page_shows_everything(): void
    {
        $account = $this->register();
        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin/website/student-accounts')->assertOk()->assertSee(route('admin.studentAccountsView', $account->id));
        $this->actingAs($admin)->get('/admin/website/student-accounts/'.$account->id)->assertOk()
            ->assertSee('Registration Details')->assertSee('Approval Student')->assertSee(trainingCourses()->first()->course('title'))
            ->assertSee('Courses')->assertSee('Payments')->assertSee('Certificates')->assertSee('Waiting for Approval');
        $this->actingAs($admin)->get('/admin/website/student-accounts/99999999')->assertRedirect(route('admin.studentAccounts'));
        // Deleting from the student page goes back to the list
        $this->actingAs($admin)->from('/admin/website/student-accounts/'.$account->id)->post('/admin/website/student-accounts/delete/'.$account->id)
            ->assertRedirect(route('admin.studentAccounts'));
    }

    public function test_reject_deletes_the_registration(): void
    {
        $account = $this->register();
        $this->actingAs($this->admin())->post('/admin/website/student-accounts/delete/'.$account->id)->assertSessionHas('success');
        $this->assertNull(StudentAccount::find($account->id));
    }

    public function test_students_cannot_approve_themselves(): void
    {
        $account = $this->register();
        $this->post('/admin/website/student-accounts/approve/'.$account->id)->assertRedirect();
        $this->assertNull($account->fresh()->approved_at);
    }
}
