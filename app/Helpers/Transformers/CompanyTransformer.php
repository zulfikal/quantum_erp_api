<?php

namespace App\Helpers\Transformers;

use App\Models\Accounting\CompanyBank;
use App\Models\HRM\Company;
use App\Models\HRM\CompanyBranch;
use App\Models\HRM\Department;
use App\Models\HRM\Designation;

class CompanyTransformer
{
    public static function company(Company $company)
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'register_number' => $company->register_number,
            'tin_number' => $company->tin_number,
        ];
    }

    public static function branch(CompanyBranch $companyBranch)
    {
        return [
            'id' => $companyBranch->id,
            'company_id' => $companyBranch->company_id,
            'name' => $companyBranch->name,
            'address_1' => $companyBranch->address_1,
            'city' => $companyBranch->city,
            'state' => $companyBranch->state,
            'zip_code' => $companyBranch->zip_code,
            'country' => $companyBranch->country,
            'phone' => $companyBranch->phone,
            'email' => $companyBranch->email,
        ];
    }

    public static function designation(Designation $designation)
    {
        return [
            'id' => $designation->id,
            'company_id' => $designation->company_id,
            'name' => $designation->name,
            'code' => $designation->code,
        ];
    }

    public static function department(Department $department)
    {
        return [
            'id' => $department->id,
            'company_id' => $department->company_id,
            'name' => $department->name,
        ];
    }

    public static function bank(CompanyBank $companyBank)
    {
        return [
            'id' => $companyBank->id,
            'bank' => BankTransformer::bank($companyBank->bank),
            'account_number' => $companyBank->account_number,
            'holder_name' => $companyBank->holder_name,
            'type' => $companyBank->type,
            'status' => $companyBank->status,
        ];
    }
}
