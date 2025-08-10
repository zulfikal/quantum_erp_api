<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\CompanyBranch;

class CompanyBranchTransformer
{
    public static function transform(CompanyBranch $companyBranch)
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
        ];
    }
}