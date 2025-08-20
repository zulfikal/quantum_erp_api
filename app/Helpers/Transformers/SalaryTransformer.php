<?php

namespace App\Helpers\Transformers;

use App\Models\Salary\SalaryType;
use App\Models\Salary\SalaryItem;
use App\Models\Salary\SalaryProcess;
use App\Models\HRM\Employee;
use App\Helpers\Transformers\CompanyTransformer;
use App\Models\Salary\SalaryProcessItem;

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

    public static function employee(Employee $employee)
    {
        return [
            'id' => $employee->id,
            'full_name' => $employee->full_name,
            'employee_status' => $employee->status,
            'company_branch' => CompanyTransformer::branch($employee->companyBranch),
            'designation' => $employee->designation->name,
            'department' => $employee->department->name,
            'basic_salary' => $employee->basic_salary,
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

    public static function salaryProcessItem(SalaryProcessItem $salaryProcessItem)
    {
        return [
            'id' => $salaryProcessItem->id,
            'employee' => $salaryProcessItem->employee->full_name,
            'date' => $salaryProcessItem->date->format('Y-m-d'),
            'basic_amount' => number_format($salaryProcessItem->basic_amount, 2),
            'allowance_amount' => number_format($salaryProcessItem->allowance_amount, 2),
            'deduction_amount' => number_format($salaryProcessItem->deduction_amount, 2),
            'company_contribution_amount' => number_format($salaryProcessItem->company_contribution_amount, 2),
            'total_amount' => number_format($salaryProcessItem->total_amount, 2),
        ];
    }
}
