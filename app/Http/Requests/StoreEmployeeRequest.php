<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee.user_id' => 'nullable|exists:users,id',
            'employee.designation_id' => 'required|exists:designations,id',
            'employee.department_id' => 'required|exists:departments,id',
            'employee.company_branch_id' => 'required|exists:company_branches,id',
            'employee.nric_number' => 'required',
            'employee.first_name' => 'required',
            'employee.last_name' => 'required',
            'employee.email' => 'required|email',
            'employee.phone' => 'nullable',
            'employee.basic_salary' => 'nullable',
            'employee.gender' => 'required|in:male,female',
            'employee.marital_status' => 'required|in:single,married,divorced',
            'employee.nationality' => 'required',
            'employee.religion' => 'required',
            'employee.address_1' => 'required',
            'employee.city' => 'required',
            'employee.state' => 'required',
            'employee.zip_code' => 'required',
            'employee.country' => 'required',
            'employee.register_number' => 'nullable',
            'employee.status' => 'required|in:active,inactive',
            'bank.bank_id' => 'nullable|exists:banks,id',
            'bank.account_number' => 'required_with:bank.bank_id',
            'bank.holder_name' => 'required_with:bank.bank_id',
        ];
    }
}
