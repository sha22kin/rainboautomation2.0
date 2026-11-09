<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Email with the password reset link of a website student account
class StudentResetPassword extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('student.password.reset', ['token' => $this->token, 'email' => $notifiable->email]);
        $site = general()->title ?: config('app.name');

        return (new MailMessage)
            ->subject('Reset your student account password — '.$site)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('We received a request to reset the password of your '.$site.' student account.')
            ->action('Reset Password', $url)
            ->line('This link is valid for 60 minutes. If you did not ask for it, you can ignore this email: your password stays the same.');
    }
}
