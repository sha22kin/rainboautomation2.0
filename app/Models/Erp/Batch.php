<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'erp_batches';
    protected $guarded = ['id'];
    protected $casts = ['start' => 'date'];

    // Display number used across the ERP, e.g. BAT-01
    public function getCodeAttribute(){
        return 'BAT-'.str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }

    public function course(){
        return $this->belongsTo(\App\Models\PostExtra::class, 'course_id')->where('type', 4);
    }

    public function trainer(){
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
}
