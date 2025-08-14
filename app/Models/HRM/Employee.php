<?php

namespace App\Models\HRM;

use App\Models\Salary\SalaryItem;
use App\Models\Salary\SalaryProcessItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'company_branch_id',
        'designation_id',
        'nric_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'marital_status',
        'nationality',
        'religion',
        'address_1',
        'city',
        'state',
        'zip_code',
        'country',
        'register_number',
        'department_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function company(): HasOneThrough
    {
        return $this->hasOneThrough(
            Company::class,
            CompanyBranch::class,
            'id', // Foreign key on company_branches table.
            'id', // Foreign key on companies table.
            'company_branch_id', // Foreign key on employees table.
            'company_id' // Foreign key on company_branches table.
        );
    }

    public function companyBranch()
    {
        return $this->belongsTo(CompanyBranch::class, 'company_branch_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function bankAccount()
    {
        return $this->hasOne(EmployeeBank::class, 'employee_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'id');
    }

    public function salaryItems(): HasMany
    {
        return $this->hasMany(SalaryItem::class, 'employee_id', 'id');
    }

    public function salaryProcessItems(): HasMany
    {
        return $this->hasMany(SalaryProcessItem::class, 'employee_id', 'id');
    }

    /**
     * Get salary items by type
     *
     * @param string $type The type of salary item ('allowance' or 'deduction')
     * @param string $relationClass The relation class to use
     * @return float
     */
    private function getSalaryItemsByType(string $type, string $relationClass = SalaryItem::class): float
    {
        return $this->hasMany($relationClass, 'employee_id', 'id')
            ->whereHas('salaryType', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->sum('amount');
    }

    /**
     * Calculate total salary based on basic salary, allowances and deductions
     *
     * @param float $allowances Total allowances amount
     * @param float $deductions Total deductions amount
     * @return float
     */
    private function calculateTotal(float $allowances, float $deductions): float
    {
        return $this->basic_salary + $allowances - $deductions;
    }

    /**
     * Get total salary item deductions
     *
     * @return float
     */
    public function salaryItemDeductions(): float
    {
        return $this->getSalaryItemsByType('deduction');
    }

    /**
     * Get total salary item allowances
     *
     * @return float
     */
    public function salaryItemAllowances(): float
    {
        return $this->getSalaryItemsByType('allowance');
    }

    public function salaryItemContributions(): float
    {
        return $this->getSalaryItemsByType('company_contribution');
    }

    /**
     * Calculate total salary from basic salary, allowances and deductions
     *
     * @return float
     */
    public function salaryItemTotal(): float
    {
        $allowances = $this->salaryItemAllowances();
        $deductions = $this->salaryItemDeductions();

        return $this->calculateTotal($allowances, $deductions);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
