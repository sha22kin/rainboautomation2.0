<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'erp_attendances';
    protected $guarded = ['id'];
    protected $casts = ['date' => 'date'];

    // Display number used across the ERP, e.g. ATT-01
    public function getCodeAttribute(){
        return 'ATT-'.str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function batch(){
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
