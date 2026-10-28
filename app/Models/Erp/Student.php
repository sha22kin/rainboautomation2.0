<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'erp_students';
    protected $guarded = ['id'];
    protected $casts = ['fee' => 'float', 'admitted_on' => 'date'];

    // Display number used across the ERP, e.g. STU-1001
    public function getCodeAttribute(){
        return 'STU-'.str_pad($this->id + 1000, 4, '0', STR_PAD_LEFT);
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

    // Remaining fee: agreed course fee less everything paid (never below zero)
    public function due(){
        return max(0, $this->fee - $this->payments->sum('amount'));
    }
}
