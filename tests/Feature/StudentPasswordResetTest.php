<?php

namespace Tests\Feature;

use App\Models\StudentAccount;
use App\Notifications\StudentResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

// Student forgot / reset password. Runs inside a transaction that is rolled back; no email is really sent.
class StudentPasswordResetTest extends TestCase
{
    use DatabaseTransactions;

    private StudentAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(['translate.googleapis.com/*' => Http::response([[['অনুবাদ', 'x']]], 200)]);
        Notification::fake();
        $this->account = StudentAccount::create(['name' => 'Reset Test Student', 'email' => 'reset-test@example.com', 'phone' => '01712340077', 'phone_key' => '01712340077', 'password' => 'secret123'])->refresh();
    }

    private function token(): string
    {
        $this->post('/student/forgot-password', ['email' => 'reset-test@example.com']);
        $token = null;
        Notification::assertSentTo($this->account, StudentResetPassword::class, function ($n) use (&$token) {
            $token = $n->token;
            return true;
        });
        return $token;
    }

    public function test_login_page_links_to_forgot_password(): void
    {
        $this->get('/student/login')->assertSee(route('student.password.forgot'))->assertSee('Forgot password?');
        $this->get('/student/forgot-password')->assertOk()->assertSee('Send Reset Link');
    }

    public function test_reset_link_is_emailed_with_the_student_reset_page(): void
    {
        $this->from('/student/forgot-password')->post('/student/forgot-password', ['email' => 'Reset-Test@Example.com'])
            ->assertRedirect('/student/forgot-password')->assertSessionHas('success');
        Notification::assertSentTo($this->account, StudentResetPassword::class, function ($n) {
            $mail = $n->toMail($this->account);
            return str_contains($mail->actionUrl, '/student/reset-password/'.$n->token) && str_contains($mail->actionUrl, 'email=reset-test%40example.com');
        });
    }

    public function test_unknown_email_gets_the_same_message_and_no_email(): void
    {
        $this->post('/student/forgot-password', ['email' => 'nobody-here@example.com'])
            ->assertSessionHas('success', 'If a student account uses this email, a password reset link has been sent to it. Please check your inbox (and spam folder).');
        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_the_link_once(): void
    {
        $token = $this->token();
        $this->get('/student/reset-password/'.$token.'?email=reset-test@example.com')->assertOk()->assertSee('Set a new password')->assertSee('value="reset-test@example.com"', false);

        $this->post('/student/reset-password', ['token' => $token, 'email' => 'reset-test@example.com', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertRedirect('/student/login')->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpass123', $this->account->fresh()->password));
        $this->post('/student/login', ['email' => 'reset-test@example.com', 'password' => 'newpass123'])->assertRedirect('/student/profile');
        auth('student')->logout();

        // The same link cannot be used again
        $this->from('/student/reset-password/'.$token)->post('/student/reset-password', ['token' => $token, 'email' => 'reset-test@example.com', 'password' => 'other12345', 'password_confirmation' => 'other12345'])
            ->assertSessionHas('error');
        $this->assertTrue(Hash::check('newpass123', $this->account->fresh()->password));
    }

    public function test_wrong_token_weak_password_and_expired_link_are_refused(): void
    {
        $token = $this->token();
        $this->post('/student/reset-password', ['token' => 'wrong-token', 'email' => 'reset-test@example.com', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])->assertSessionHas('error');
        $this->post('/student/reset-password', ['token' => $token, 'email' => 'reset-test@example.com', 'password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');

        $this->travel(61)->minutes();
        $this->post('/student/reset-password', ['token' => $token, 'email' => 'reset-test@example.com', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])->assertSessionHas('error');
        $this->assertTrue(Hash::check('secret123', $this->account->fresh()->password));
    }

    public function test_one_link_a_minute(): void
    {
        $this->post('/student/forgot-password', ['email' => 'reset-test@example.com'])->assertSessionHas('success');
        $this->post('/student/forgot-password', ['email' => 'reset-test@example.com'])->assertSessionHas('error', 'A reset link was sent a moment ago. Please wait a minute before asking again.');
        Notification::assertSentToTimes($this->account, StudentResetPassword::class, 1);
    }
}
