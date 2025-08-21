<?php

namespace App\Helpers\Constants;

use App\Models\HRM\Company;
use App\Models\HRM\CompanyBranch;
use App\Models\HRM\Department;

final class CompanyStaticData
{
    public static function branch(CompanyBranch $companyBranch)
    {
        return [
            'id' => $companyBranch->id,
            'name' => $companyBranch->name,
            'address_1' => $companyBranch->address_1,
            'city' => $companyBranch->city,
            'state' => $companyBranch->state,
            'zip_code' => $companyBranch->zip_code,
            'country' => $companyBranch->country,
            'phone' => $companyBranch->phone,
        ];
    }

    public static function branchList(CompanyBranch $companyBranch)
    {
        return [
            'id' => $companyBranch->id,
            'name' => $companyBranch->name,
        ];
    }

    public static function company(Company $company)
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'register_number' => $company->register_number,
            'tin_number' => $company->tin_number,
            'branches' => $company->branches->transform(fn($q) => self::branch($q)),
        ];
    }

    public static function department(Department $department)
    {
        return [
            'id' => $department->id,
            'name' => $department->name,
        ];
    }

    public static function bankType(): array
    {
        return [
            ['code' => 'current', 'name' => 'Current Account'],
            ['code' => 'saving', 'name' => 'Saving Account'],
        ];
    }

    public static function bankStatus(): array
    {
        return [
            ['code' => 'active', 'name' => 'Active'],
            ['code' => 'inactive', 'name' => 'Inactive'],
        ];
    }
}
