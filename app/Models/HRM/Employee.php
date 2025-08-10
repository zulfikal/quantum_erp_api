<?php

namespace App\Models\HRM;

use App\Models\Salary\SalaryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'designation_id',
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
        'bank_name',
        'bank_account_number',
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

    public function salaryItems() : HasMany
    {
        return $this->hasMany(SalaryItem::class, 'employee_id', 'id');
    }
}
