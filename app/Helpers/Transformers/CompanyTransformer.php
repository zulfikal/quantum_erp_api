<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Company;

class CompanyTransformer
{
    public static function transform(Company $company)
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'register_number' => $company->register_number,
            'tin_number' => $company->tin_number,
        ];
    }
}