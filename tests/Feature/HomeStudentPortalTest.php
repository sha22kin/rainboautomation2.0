<?php

namespace Tests\Feature;

use App\Models\StudentAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// Home page "Student Portal & Certificate Services": its forms use the real student register / login and the certificate finder
class HomeStudentPortalTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
    }

    public function test_home_shows_the_portal_and_steps_sections(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Student Portal &amp; Certificate Services', false)
            ->assertSee('A simpler journey from registration to certification')
            ->assertSee(route('student.register'))->assertSee(route('student.login'))->assertSee(route('certificates.find'));
    }

    public function test_register_from_home_creates_the_account(): void
    {
        $this->from('/')->post('/student/register', ['_portal' => 'register', 'name' => 'Home Portal Student', 'phone' => '01712340991', 'email' => 'home-portal@example.com', 'password' => 'secret123', 'password_confirmation' => 'secret123', 'agree' => '1', 'profession' => 'Student', 'preferred_course_id' => trainingCourses()->first()->id])
            ->assertRedirect('/student/login');
        $this->assertTrue(StudentAccount::where('email', 'home-portal@example.com')->exists());
    }

    public function test_failed_register_comes_back_to_home_with_the_message(): void
    {
        $this->from('/')->post('/student/register', ['_portal' => 'register', 'name' => 'Home Portal Student', 'phone' => '123', 'email' => 'home-portal2@example.com', 'password' => 'secret123', 'password_confirmation' => 'secret123', 'agree' => '1', 'profession' => 'Student', 'preferred_course_id' => trainingCourses()->first()->id])
            ->assertRedirect('/');
        $this->get('/')->assertSee('data-sp-open="register"', false)->assertSee('Please enter a valid Bangladeshi mobile number');
    }

    public function test_failed_login_comes_back_to_home_with_the_message(): void
    {
        $this->from('/')->post('/student/login', ['_portal' => 'login', 'email' => 'nobody-home@example.com', 'password' => 'wrongpass1'])->assertRedirect('/');
        $this->get('/')->assertSee('data-sp-open="login"', false)->assertSee('The email or password is incorrect.');
    }
}
