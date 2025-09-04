<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:employees,email,' . auth()->user()->employee->id,
            'phone' => 'nullable',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:single,married,divorced',
            'nationality' => 'required',
            'religion' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
        ];
    }
}
