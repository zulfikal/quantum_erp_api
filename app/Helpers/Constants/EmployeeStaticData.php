<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\CompanyTransformer;
use App\Models\HRM\Company;

class EmployeeStaticData
{
    public static function gender(): array
    {
        return [
            ['code' => 'male', 'name' => 'Male'],
            ['code' => 'female', 'name' => 'Female'],
            ['code' => 'other', 'name' => 'Other'],
        ];
    }

    public static function maritalStatus(): array
    {
        return [
            ['code' => 'single', 'name' => 'Single'],
            ['code' => 'married', 'name' => 'Married'],
            ['code' => 'divorced', 'name' => 'Divorced'],
            ['code' => 'widowed', 'name' => 'Widowed'],
        ];
    }

    public static function status(): array
    {
        return [
            ['code' => 'active', 'name' => 'Active'],
            ['code' => 'inactive', 'name' => 'Inactive'],
        ];
    }

    public static function designation(Company $company)
    {
        $designations = $company->designations;

        return $designations->transform(fn($q) => CompanyTransformer::designation($q));
    }

    public static function department(Company $company)
    {
        $departments = $company->departments;

        return $departments->transform(fn($q) => CompanyTransformer::department($q));
    }

    public static function branch(Company $company)
    {
        $branches = $company->branches;

        return $branches->transform(fn($q) => CompanyTransformer::branch($q));
    }
}
