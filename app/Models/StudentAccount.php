<?php

namespace App\Models;

use App\Models\Erp\Certificate;
use App\Models\Erp\Student;
use Illuminate\Foundation\Auth\User as Authenticatable;

// Website student account ("student" auth guard), separate from the CMS users table
class StudentAccount extends Authenticatable
{
    use \Illuminate\Notifications\Notifiable;

    // Forgot password: the email links to the student reset page
    public function sendPasswordResetNotification($token){
        $this->notify(new \App\Notifications\StudentResetPassword($token));
    }

    protected $table = 'student_accounts';
    protected $fillable = ['name', 'email', 'phone', 'phone_key', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed', 'last_login_at' => 'datetime', 'status' => 'boolean'];

    // Digits of a Bangladeshi mobile number, last 11 (01XXXXXXXXX), used to compare numbers written differently
    public static function phoneKey($phone){
        $digits = preg_replace('/\D/', '', (string) $phone);
        return substr($digits, -11);
    }

    // Registration number shown to the student and the admin, e.g. ACC-1001
    public function getCodeAttribute(){
        return 'ACC-'.str_pad($this->id + 1000, 4, '0', STR_PAD_LEFT);
    }

    // Profile photo URL, or null (the page then shows the initials)
    public function photoUrl(){
        return $this->photo && is_file(public_path($this->photo)) ? asset($this->photo).'?v='.filemtime(public_path($this->photo)) : null;
    }

    public function initials(){
        return collect(preg_split('/\s+/', trim($this->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }

    // ERP student records (course admissions) an admin linked to this account
    public function enrollments(){
        return $this->hasMany(Student::class, 'account_id');
    }

    public function certificates(){
        return Certificate::whereIn('student_id', $this->enrollments()->pluck('id'));
    }

    // Unlinked ERP student records with the same mobile number (shown to the admin as link suggestions)
    public function suggestedEnrollments(){
        return Student::whereNull('account_id')->whereNotNull('phone')->get()
            ->filter(fn ($s) => static::phoneKey($s->phone) === $this->phone_key)->values();
    }
}
