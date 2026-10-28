<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'erp_payments';
    protected $guarded = ['id'];
    protected $casts = ['amount' => 'float', 'date' => 'date'];

    // Display number used across the ERP, e.g. PAY-001
    public function getCodeAttribute(){
        return 'PAY-'.str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
