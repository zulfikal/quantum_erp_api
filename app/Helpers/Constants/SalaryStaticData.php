<?php

namespace App\Helpers\Constants;

use App\Models\Salary\SalaryType;
use App\Helpers\Transformers\SalaryTransformer;

final class SalaryStaticData
{
    public static function salaryType()
    {
        $types = SalaryType::where('company_id', auth()->user()->employee->company->id)->orWhere('company_id', null)->get();
        return $types->transform(fn($type) => SalaryTransformer::salaryType($type));
    }
}
