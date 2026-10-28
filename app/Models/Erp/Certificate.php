<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'erp_certificates';
    protected $guarded = ['id'];
    protected $casts = ['issued' => 'date', 'gpa' => 'float', 'cgpa' => 'float'];

    const GRADES = ['A+', 'A', 'A-', 'B', 'C', 'Pass', 'Fail'];

    // "GPA 3.75 · CGPA 3.80" from the points that are filled, or an empty text when neither is
    public function points(){
        return collect(['GPA' => $this->gpa, 'CGPA' => $this->cgpa])
            ->filter(fn ($p) => $p !== null)
            ->map(fn ($p, $label) => $label.' '.number_format($p, 2))
            ->implode(' · ');
    }

    // Display number used across the ERP, e.g. CERT-01
    public function getCodeAttribute(){
        return 'CERT-'.str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course(){
        return $this->belongsTo(\App\Models\PostExtra::class, 'course_id')->where('type', 4);
    }
}
