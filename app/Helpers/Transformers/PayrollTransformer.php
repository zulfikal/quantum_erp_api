<?php

namespace App\Helpers\Transformers;

use App\Models\Salary\SalaryProcessItem;
use App\Helpers\Transformers\EmployeeTransformer;
use App\Helpers\Transformers\SalaryTransformer;
use App\Models\Salary\SalaryProcessItemDetail;

final class PayrollTransformer
{
    public static function payrollList(SalaryProcessItem $salaryProcessItem): array
    {
        return [
            'id' => $salaryProcessItem->id,
            'employee' => $salaryProcessItem->employee->full_name,
            'company' => $salaryProcessItem->employee->companyBranch->company->name,
            'branch' => $salaryProcessItem->employee->companyBranch->name,
            'date' => $salaryProcessItem->date->format('Y-m-d'),
            'basic_amount' => $salaryProcessItem->basic_amount,
            'allowance_amount' => $salaryProcessItem->allowance_amount,
            'deduction_amount' => $salaryProcessItem->deduction_amount,
            'company_contribution_amount' => $salaryProcessItem->company_contribution_amount,
            'total_amount' => $salaryProcessItem->total_amount,
        ];
    }

    public static function payrollItem(SalaryProcessItemDetail $salaryProcessItemDetail): array
    {
        return [
            'id' => $salaryProcessItemDetail->id,
            'salary_type' => SalaryTransformer::salaryType($salaryProcessItemDetail->salaryType),
            'amount' => $salaryProcessItemDetail->amount,
        ];
    }

    public static function payrollDetail(SalaryProcessItem $salaryProcessItem): array
    {
        // Fetch all details with their salary types in a single query
        $allDetails = $salaryProcessItem->salaryProcessItemDetails()
            ->with('salaryType')
            ->get()
            ->groupBy(function ($detail) {
                return $detail->salaryType->type;
            });
        
        // Organize details by type
        $deductionItems = $allDetails->get('deduction', collect());
        $allowanceItems = $allDetails->get('allowance', collect());
        $contributionItems = $allDetails->get('company_contribution', collect());
        
        return [
            'id' => $salaryProcessItem->id,
            'employee' => EmployeeTransformer::transform($salaryProcessItem->employee),
            'date' => $salaryProcessItem->date->format('Y-m-d'),
            'basic_salary' => $salaryProcessItem->basic_amount,
            'deductions' => [
                'total' => $salaryProcessItem->deduction_amount,
                'items' => $deductionItems->transform(function ($detail) {
                    return self::payrollItem($detail);
                }),
            ],
            'allowances' => [
                'total' => $salaryProcessItem->allowance_amount,
                'items' => $allowanceItems->transform(function ($detail) {
                    return self::payrollItem($detail);
                }),
            ],
            'company_contributions' => [
                'total' => $salaryProcessItem->company_contribution_amount,
                'items' => $contributionItems->transform(function ($detail) {
                    return self::payrollItem($detail);
                }),
            ],
            'total_salary' => $salaryProcessItem->total_amount
        ];
    }
}
