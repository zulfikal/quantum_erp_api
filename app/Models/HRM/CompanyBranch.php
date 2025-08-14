<?php

namespace App\Models\HRM;

use App\Models\Salary\SalaryProcess;
use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'address_1',
        'city',
        'state',
        'zip_code',
        'country',
        'phone',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function salaryProcesses()
    {
        return $this->hasMany(SalaryProcess::class);
    }
}
