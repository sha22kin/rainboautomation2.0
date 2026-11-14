<?php

namespace Tests\Feature;

use App\Models\Erp\Certificate;
use App\Models\Erp\Payment;
use App\Models\Erp\Student;
use App\Models\Permission;
use App\Models\StudentAccount;
use App\Models\User;
use App\Support\Erp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

// Website student accounts: register, login, profile, security and the admin "Registered Students" page.
// Runs against the configured database inside a transaction that is rolled back.
class StudentPortalTest extends TestCase
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
            'name' => 'Test Student Karim', 'email' => 'Test.Student@Example.com', 'phone' => '01712345699',
            'password' => 'secret123', 'password_confirmation' => 'secret123', 'agree' => '1',
            'profession' => 'Student', 'preferred_course_id' => trainingCourses()->first()->id,
        ]);
        if($approve && ($account = StudentAccount::where('email', strtolower(trim($data['email'] ?? 'Test.Student@Example.com')))->whereNull('approved_at')->first())){
            $account->approve();
        }
        return $response;
    }

    private function registerAndLogin()
    {
        $this->register();
        return $this->post('/student/login', ['email' => 'test.student@example.com', 'password' => 'secret123']);
    }

    private function admin(): User
    {
        $role = Permission::where('permission', 'like', '%appsSetting%')->first();
        $user = new User(['name' => 'Portal Test Admin', 'email' => 'portal-test-admin@example.com', 'password' => Hash::make('secret-pass')]);
        $user->admin = 1;
        $user->status = 1;
        $user->permission_id = $role?->id;
        $user->save();
        return $user;
    }

    public function test_footer_shows_student_links(): void
    {
        $this->get('/')->assertSee(route('student.login'))->assertSee('Student Register');
    }

    public function test_student_can_register_and_see_profile(): void
    {
        $this->get('/student/register')->assertOk()->assertSee('Create your student account');
        // Registration shows a success message on the login page, with the email filled in
        $this->register()->assertRedirect('/student/login');
        $this->assertGuest('student');
        $this->get('/student/login')->assertOk()->assertSee('Registration successful!')->assertSee('value="test.student@example.com"', false);

        $account = StudentAccount::where('email', 'test.student@example.com')->firstOrFail();
        $this->assertNotSame('secret123', $account->password);
        $this->assertTrue(Hash::check('secret123', $account->password));
        $this->assertSame('01712345699', $account->phone_key);

        // Login opens the profile
        $this->post('/student/login', ['email' => 'test.student@example.com', 'password' => 'secret123'])->assertRedirect('/student/profile');
        $this->assertAuthenticated('student');
        $this->assertGuest('web');
        $this->get('/student/profile')->assertOk()->assertSee('Test Student Karim')->assertSee('No course is linked');
        $this->get('/')->assertSee('My Student Profile');
    }

    public function test_registration_is_validated_and_the_reason_is_shown(): void
    {
        $this->register(['email' => 'not-an-email'])->assertSessionHasErrors('email');
        $this->register(['phone' => '12345'])->assertSessionHasErrors('phone');
        $this->register(['password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');
        $this->register(['password_confirmation' => 'different1'])->assertSessionHasErrors('password');
        $this->register(['name' => '<script>x</script>'])->assertSessionHasErrors('name');
        $this->register(['agree' => null])->assertSessionHasErrors('agree');
        $this->assertSame(0, StudentAccount::where('email', 'test.student@example.com')->count());

        // The failed form shows a message box at the top
        $this->from('/student/register')->register(['phone' => '12345']);
        $this->get('/student/register')->assertSee('Registration failed.')->assertSee('valid Bangladeshi mobile number');
    }

    public function test_the_same_email_or_phone_cannot_register_twice(): void
    {
        $this->register()->assertRedirect('/student/login');
        $this->from('/student/register')->register(['phone' => '01812345699'])->assertSessionHasErrors('email');
        $this->get('/student/register')->assertSee('An account with this email already exists');
        $this->register(['email' => 'other@example.com', 'phone' => '+8801712345699'])->assertSessionHasErrors('phone_key');
        $this->register(['email' => 'TEST.student@example.com ', 'phone' => '01912345699'])->assertSessionHasErrors('email');
        $this->assertSame(1, StudentAccount::where('email', 'test.student@example.com')->count());
        $this->assertSame(1, StudentAccount::where('phone_key', '01712345699')->count());
    }

    public function test_at_most_five_accounts_an_hour_from_one_connection(): void
    {
        RateLimiter::clear('student-register:127.0.0.1');
        for ($i = 1; $i <= 5; $i++) {
            $this->register(['email' => "rate$i@example.com", 'phone' => '0171234560'.$i])->assertRedirect('/student/login');
        }
        $this->from('/student/register')->register(['email' => 'rate6@example.com', 'phone' => '01712345606'])->assertRedirect('/student/register');
        $this->assertStringContainsString('Too many accounts were registered', session('error'));
        $this->assertSame(0, StudentAccount::where('email', 'rate6@example.com')->count());
        RateLimiter::clear('student-register:127.0.0.1');
    }

    public function test_bot_filling_the_hidden_field_gets_a_message(): void
    {
        $this->from('/student/register')->register(['ra_hp_field' => 'http://spam.example'])->assertRedirect('/student/register');
        $this->assertSame('Registration could not be completed. Please try again.', session('error'));
        $this->assertSame(0, StudentAccount::where('email', 'test.student@example.com')->count());
        // A field named "website" (often auto-filled by browsers) no longer blocks a real student
        $this->register(['website' => 'https://example.com'])->assertRedirect('/student/login');
        $this->assertSame(1, StudentAccount::where('email', 'test.student@example.com')->count());
    }

    public function test_failed_login_shows_a_message(): void
    {
        $this->register();
        $this->from('/student/login')->post('/student/login', ['email' => 'test.student@example.com', 'password' => 'wrong-pass1'])->assertRedirect('/student/login');
        $this->get('/student/login')->assertSee('The email or password is incorrect.');
        RateLimiter::clear('student-login:test.student@example.com|127.0.0.1');
    }

    public function test_login_logout_and_rate_limit(): void
    {
        $this->registerAndLogin();
        $this->post('/student/logout')->assertRedirect('/student/login');
        $this->assertGuest('student');
        $this->get('/student/profile')->assertRedirect('/student/login');

        $this->post('/student/login', ['email' => 'test.student@example.com', 'password' => 'secret123'])->assertRedirect('/student/profile');
        $this->assertAuthenticated('student');
        auth('student')->logout();

        $key = 'student-login:test.student@example.com|127.0.0.1';
        RateLimiter::clear($key);
        for ($i = 0; $i < 5; $i++) {
            $this->post('/student/login', ['email' => 'test.student@example.com', 'password' => 'wrong-pass1'])->assertSessionHas('error', 'The email or password is incorrect.');
        }
        // Locked even with the right password after 5 wrong ones
        $this->post('/student/login', ['email' => 'test.student@example.com', 'password' => 'secret123']);
        $this->assertGuest('student');
        $this->assertStringContainsString('Too many login attempts', session('error'));
        RateLimiter::clear($key);
    }

    public function test_student_cannot_open_the_cms(): void
    {
        $this->registerAndLogin();
        $this->get('/admin')->assertRedirect('/admin-login');
        $this->get('/admin/website/student-accounts')->assertRedirect('/admin-login');
        $this->post('/admin-login', ['username' => 'test.student@example.com', 'password' => 'secret123'])->assertSessionHas('error');
        $this->assertGuest('web');
    }

    public function test_linked_courses_and_own_certificates_only(): void
    {
        $course = Erp::courses()->first();
        $this->registerAndLogin();
        $account = StudentAccount::where('email', 'test.student@example.com')->firstOrFail();

        $mine = Student::create(['name' => 'Test Student Karim', 'phone' => '01712-345699', 'course_id' => $course->id, 'fee' => 12000, 'admitted_on' => '2026-11-01']);
        Payment::create(['student_id' => $mine->id, 'amount' => 5000, 'date' => '2026-11-02', 'method' => 'Cash']);
        $myCert = Certificate::create(['student_id' => $mine->id, 'course_id' => $course->id, 'issued' => '2026-11-05', 'grade' => 'A+']);
        $other = Student::create(['name' => 'Someone Else', 'phone' => '01900000000', 'course_id' => $course->id, 'fee' => 8000]);
        $otherCert = Certificate::create(['student_id' => $other->id, 'course_id' => $course->id, 'issued' => '2026-11-05', 'grade' => 'B']);

        // Same phone is only a suggestion: nothing shows until an admin links it
        $this->get('/student/profile')->assertSee('No course is linked')->assertDontSee($myCert->code);
        $this->assertTrue($account->suggestedEnrollments()->contains('id', $mine->id));

        $mine->update(['account_id' => $account->id]);
        $this->get('/student/profile')->assertOk()->assertSee($course->course('title'))->assertSee('৳ 7,000')->assertSee($myCert->code)->assertSee('A+');
        $this->get('/student/certificates/'.$myCert->id)->assertOk();
        $this->get('/student/certificates/'.$otherCert->id)->assertNotFound();
    }

    public function test_admin_sees_registered_students_and_links_blocks_deletes(): void
    {
        $course = Erp::courses()->first();
        $this->register();
        auth('student')->logout();
        $account = StudentAccount::where('email', 'test.student@example.com')->firstOrFail();
        $record = Student::create(['name' => 'Test Student Karim', 'phone' => '8801712345699', 'course_id' => $course->id, 'fee' => 12000]);

        $admin = $this->admin();
        $this->actingAs($admin);
        $this->get('/admin/website/student-accounts')->assertOk()->assertSee('Test Student Karim');
        $this->get('/admin/website/student-accounts/'.$account->id)->assertOk()->assertSee($record->code);
        $this->get('/admin/website/student-accounts/link/'.$account->id)->assertStatus(405);

        $this->post('/admin/website/student-accounts/link/'.$account->id, ['student' => $record->id]);
        $this->assertSame($account->id, $record->fresh()->account_id);

        $this->post('/admin/website/student-accounts/block/'.$account->id);
        $this->assertFalse($account->fresh()->status);
        $this->post('/student/login', ['email' => 'test.student@example.com', 'password' => 'secret123'])->assertSessionHas('error');
        $this->assertGuest('student');

        $this->post('/admin/website/student-accounts/delete/'.$account->id);
        $this->assertNull($account->fresh());
        $this->assertNull($record->fresh()->account_id);
    }
}
