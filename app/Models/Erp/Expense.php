<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'erp_expenses';
    protected $guarded = ['id'];
    protected $casts = ['amount' => 'float', 'date' => 'date'];

    // Display number used across the ERP, e.g. EXP-001
    public function getCodeAttribute(){
        return 'EXP-'.str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
