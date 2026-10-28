<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $table = 'erp_labs';
    protected $guarded = ['id'];

    // Display number used across the ERP, e.g. LAB-01
    public function getCodeAttribute(){
        return 'LAB-'.str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }
}
