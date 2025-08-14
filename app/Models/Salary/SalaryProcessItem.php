<?php

namespace App\Models\Salary;

use App\Models\HRM\Employee;
use Illuminate\Database\Eloquent\Model;

class SalaryProcessItem extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_process_id',
        'date',
        'basic_amount',
        'allowance_amount',
        'deduction_amount',
        'company_contribution_amount',
        'total_amount',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
    
    public function salaryProcess()
    {
        return $this->belongsTo(SalaryProcess::class, 'salary_process_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function salaryProcessItemDetails()
    {
        return $this->hasMany(SalaryProcessItemDetail::class);
    }
}
