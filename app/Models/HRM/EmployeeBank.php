<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class EmployeeBank extends Model
{
    protected $fillable = [
        'employee_id',
        'bank_id',
        'account_number',
        'holder_name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
