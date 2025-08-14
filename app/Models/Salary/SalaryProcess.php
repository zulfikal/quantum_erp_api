<?php

namespace App\Models\Salary;

use App\Models\HRM\CompanyBranch;
use Illuminate\Database\Eloquent\Model;

class SalaryProcess extends Model
{
    protected $fillable = [
        'company_branch_id',
        'year',
        'month',
        'status',
    ];
    
    public function companyBranch()
    {
        return $this->belongsTo(CompanyBranch::class, 'company_branch_id', 'id');
    }

    public function salaryProcessItems()
    {
        return $this->hasMany(SalaryProcessItem::class, 'salary_process_id', 'id');
    }
}
