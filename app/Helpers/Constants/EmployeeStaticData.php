<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\CompanyTransformer;
use App\Models\HRM\Company;

class EmployeeStaticData
{
    public static function gender(): array
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
    }

    public static function maritalStatus(): array
    {
        return [
            'single' => 'Single',
            'married' => 'Married',
            'divorced' => 'Divorced',
            'widowed' => 'Widowed',
        ];
    }

    public static function status(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
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
