<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
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
            'claim_type_id' => 'required|exists:claim_types,id',
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ];
    }
}
