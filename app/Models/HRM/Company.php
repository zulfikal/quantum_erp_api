<?php

namespace App\Models\HRM;

use App\Models\BusinessPartner\Entity;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    protected $fillable = [
        'name',
        'register_number',
        'tin_number',
    ];

    public function branches() : HasMany
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function designations() : HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function departments() : HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function employees() : HasManyThrough
    {
        return $this->hasManyThrough(Employee::class, CompanyBranch::class, 'company_id', 'company_branch_id', 'id', 'id');
    }

    public function leaves() : HasManyThrough
    {
        return $this->hasManyThrough(
            Leave::class,
            CompanyBranch::class,
            'company_id', // Foreign key on company_branches table
            'employee_id', // Foreign key on leaves table
            'id', // Local key on companies table
            'id' // Local key on company_branches table (to be used with employees.company_branch_id)
        )->join('employees', 'leaves.employee_id', '=', 'employees.id')
            ->where('employees.company_branch_id', '=', DB::raw('company_branches.id'));
    }

    public function claims() : HasManyThrough
    {
        // Using hasManyThrough with a more direct approach
        // First, get all employees belonging to branches of this company
        $companyId = $this->id;
        
        // Use a closure to capture the company ID
        return $this->hasManyThrough(
            Claim::class,
            Employee::class,
            'company_branch_id', // Foreign key on employees table
            'employee_id', // Foreign key on claims table
            'id', // Local key on companies table
            'id' // Local key on employees table
        )->join('company_branches', 'employees.company_branch_id', '=', 'company_branches.id')
            ->where('company_branches.company_id', '=', $companyId);
    }

    public function entities() : HasMany
    {
        return $this->hasMany(Entity::class);
    }

    public function productCategories() : HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function products() : HasMany
    {
        return $this->hasMany(Product::class);
    }
}
