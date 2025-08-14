<?php

namespace App\Helpers\Transformers;

use App\Models\Salary\SalaryType;
use App\Models\Salary\SalaryItem;
use App\Models\Salary\SalaryProcess;
use App\Helpers\Transformers\CompanyTransformer;

class SalaryTransformer
{
    public static function salaryType(SalaryType $salaryType)
    {
        return [
            'id' => $salaryType->id,
            'name' => $salaryType->name,
            'type' => $salaryType->type,
        ];
    }

    public static function salaryItem(SalaryItem $salaryItem)
    {
        return [
            'id' => $salaryItem->id,
            'amount' => $salaryItem->amount,
            'status' => $salaryItem->status,
            'salary_type' => self::salaryType($salaryItem->salaryType),
        ];
    }

    public static function salaryProcess(SalaryProcess $salaryProcess)
    {
        return [
            'id' => $salaryProcess->id,
            'company_branch' => CompanyTransformer::branch($salaryProcess->companyBranch),
            'year' => $salaryProcess->year,
            'month' => $salaryProcess->month,
            'status' => $salaryProcess->status,
        ];
    }
}