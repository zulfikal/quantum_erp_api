<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;

class SalaryItem extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_type_id',
        'status',
        'amount',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function salaryType()
    {
        return $this->belongsTo(SalaryType::class, 'salary_type_id', 'id');
    }
}
