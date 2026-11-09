<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

// One course record of a student. A student in several courses has one record per course;
// all of them share student_no, so the student keeps one Student ID (STU-xxxx).
class Student extends Model
{
    protected $table = 'erp_students';
    protected $guarded = ['id'];
    protected $casts = ['fee' => 'float', 'admitted_on' => 'date'];

    protected static function booted(){
        // A new student (not an extra course of an existing one) gets its own number
        static::created(function ($student) {
            if(!$student->student_no){
                $student->student_no = $student->id;
                $student->saveQuietly();
            }
        });
    }

    // Student ID used across the ERP, the website profile and certificates, e.g. STU-1001 (same for every course of the student)
    public function getCodeAttribute(){
        return 'STU-'.str_pad(($this->student_no ?: $this->id) + 1000, 4, '0', STR_PAD_LEFT);
    }

    // Number of the student behind a Student ID ("STU-1003" => 3), or null
    public static function numberFromCode($code){
        $text = strtoupper(preg_replace('/\s+/', '', (string) $code));
        return preg_match('/^(?:STU-?)?(\d{4,9})$/', $text, $m) && (int) $m[1] > 1000 ? (int) $m[1] - 1000 : null;
    }

    public function course(){
        return $this->belongsTo(\App\Models\PostExtra::class, 'course_id')->where('type', 4);
    }

    public function batch(){
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'student_id');
    }

    // All course records of the same student
    public function personKey(){
        return 'student:'.($this->student_no ?: $this->id);
    }

    // Course records of this student, by student record id => course id
    public function personCourses(){
        $no = $this->student_no ?: $this->id;
        if(!$no){
            return collect();
        }
        return static::where('student_no', $no)->whereNotNull('course_id')->pluck('course_id', 'id');
    }

    // The existing student a new record with this mobile number belongs to (one with a website account first)
    public static function byPhone($phone){
        $key = \App\Models\StudentAccount::phoneKey($phone);
        if(strlen($key) < 10){
            return null;
        }
        return static::whereNotNull('phone')->orderByRaw('account_id is null')->orderBy('id')->get()
            ->first(fn ($s) => \App\Models\StudentAccount::phoneKey($s->phone) === $key);
    }

    // Remaining fee: agreed course fee less everything paid (never below zero)
    public function due(){
        return max(0, $this->fee - $this->payments->sum('amount'));
    }
}
