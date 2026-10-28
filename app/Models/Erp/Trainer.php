<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    protected $table = 'erp_trainers';
    protected $guarded = ['id'];
    protected $casts = ['rate' => 'float'];

    // Display number used across the ERP, e.g. TR-01
    public function getCodeAttribute(){
        return 'TR-'.str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }
}
