<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;

class SalaryProcessItem extends Model
{
    protected $fillable = [
        'salary_process_id',
        'employee_id',
        'salary_type_id',
        'amount',
    ];
    
    public function salaryProcess()
    {
        return $this->belongsTo(SalaryProcess::class, 'salary_process_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function salaryType()
    {
        return $this->belongsTo(SalaryType::class, 'salary_type_id', 'id');
    }
}
