<?php

namespace App\Helpers\Transformers;

use App\Models\Salary\SalaryType;
use App\Models\Salary\SalaryItem;

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
}