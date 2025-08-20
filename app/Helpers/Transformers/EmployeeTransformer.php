<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Employee;
use App\Helpers\Transformers\CompanyTransformer;

class EmployeeTransformer
{
    public static function transform(Employee $employee)
    {
        return [
            'id' => $employee->id,
            'designation' => CompanyTransformer::designation($employee->designation),
            'company' => CompanyTransformer::company($employee->company),
            'branch' => CompanyTransformer::branch($employee->companyBranch),
            'department' => CompanyTransformer::department($employee->department),
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'nric_number' => $employee->nric_number,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'basic_salary' => $employee->basic_salary,
            'gender' => $employee->gender,
            'marital_status' => $employee->marital_status,
            'nationality' => $employee->nationality,
            'religion' => $employee->religion,
            'address_1' => $employee->address_1,
            'city' => $employee->city,
            'state' => $employee->state,
            'zip_code' => $employee->zip_code,
            'country' => $employee->country,
            'register_number' => $employee->register_number,
            'status' => $employee->status,
            'bank_account' => [
                'bank' => [
                    'bank_id' => $employee->bankAccount->bank->id,
                    'name' => $employee->bankAccount->bank->name,
                    'code' => $employee->bankAccount->bank->code,
                ],
                'account_number' => $employee->bankAccount->account_number,
                'holder_name' => $employee->bankAccount->holder_name,
            ],
        ];
    }

    public static function employee(Employee $employee)
    {
        return [
            'id' => $employee->id,
            'name' => $employee->full_name,
        ];
    }
}
