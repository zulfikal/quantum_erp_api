<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'priority_id' => 'required|exists:project_priorities,id',
            'status_id' => 'required|exists:project_statuses,id',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'required|exists:employees,id',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'exists:employees,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Project name is required',
            'description.required' => 'Project description is required',
            'start_date.required' => 'Project start date is required',
            'end_date.required' => 'Project end date is required',
            'priority_id.required' => 'Project priority is required',
            'status_id.required' => 'Project status is required',
            'department_id.required' => 'Project department is required',
            'manager_id.required' => 'Project manager is required',
            'assignee_ids.required' => 'Project assignees are required',
        ];
    }
}
