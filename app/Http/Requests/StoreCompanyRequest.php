<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'register_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'tin_number' => 'nullable|string|max:255',
            'msic_code' => 'nullable|string|max:255',
            'sst_number' => 'nullable|string|max:255',
            'tourism_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'status.in' => 'The status must be active or inactive.',
        ];
    }
}
