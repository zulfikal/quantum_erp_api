<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Employee;

class EmployeeTransformer
{
    public static function transform(Employee $employee)
    {
        return [
            'id' => $employee->id,
            'designation' => $employee->designation->name,
            'company' => $employee->company->name,
            'branch' => $employee->companyBranch->name,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'phone' => $employee->phone,
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
            'bank_name' => $employee->bank_name,
            'bank_account_number' => $employee->bank_account_number,
        ];
    }
}
