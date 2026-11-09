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

    // Certificate number used across the ERP, the student profile and the certificate itself, e.g. RA-7K3P-9QX2
    // (CERT-01 style only for a certificate that has no serial yet)
    public function getCodeAttribute(){
        return $this->serial ?: 'CERT-'.str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }

    protected static function booted(){
        static::creating(function ($certificate) {
            $certificate->serial ??= static::newSerial();
        });
    }

    // Random number that cannot be guessed from another certificate's number (no 0/O or 1/I to mix up)
    public static function newSerial(){
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $letters[random_int(0, strlen($letters) - 1)];
            }
            $serial = 'RA-'.substr($code, 0, 4).'-'.substr($code, 4);
        } while (static::where('serial', $serial)->exists());
        return $serial;
    }

    // "ra 7k3p 9qx2", "RA7K3P9QX2" or "7K3P-9QX2" => RA-7K3P-9QX2; null when it cannot be a certificate number
    public static function normalizeSerial($input){
        $chars = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $input));
        if (strlen($chars) == 8) {
            $chars = 'RA'.$chars;
        }
        return preg_match('/^RA[A-Z0-9]{8}$/', $chars) ? 'RA-'.substr($chars, 2, 4).'-'.substr($chars, 6) : null;
    }

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course(){
        return $this->belongsTo(\App\Models\PostExtra::class, 'course_id')->where('type', 4);
    }
}
