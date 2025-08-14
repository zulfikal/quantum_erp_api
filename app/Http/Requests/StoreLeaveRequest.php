<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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
            'leave_type_id' => 'required|exists:leave_types,id',
            'notes' => 'nullable|string',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'dates.required' => 'Please select at least one date',
            'dates.*.date' => 'Date must be a valid date',
        ];
    }
}
