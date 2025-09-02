<?php

namespace App\Http\Requests;

use App\Models\HRM\ProjectAssignee;
use App\Models\HRM\ProjectBoard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProjectTaskRequest extends FormRequest
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
            'priority_id' => 'required|integer|exists:project_priorities,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_completed' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'assignees' => 'nullable|array',
            'assignees.*' => 'required|integer',
        ];
    }
    
    /**
     * Configure the validator instance with custom validation rules.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Only validate if assignees are provided
            if (!$this->has('assignees') || empty($this->assignees)) {
                return;
            }
            
            // Get the current project board from the route
            $projectBoard = $this->route('projectBoard');
            if (!$projectBoard instanceof ProjectBoard) {
                $validator->errors()->add('assignees', 'Invalid project board.');
                return;
            }
            
            // Get the project associated with the board
            $project = $projectBoard->project;
            if (!$project) {
                $validator->errors()->add('assignees', 'Project board is not associated with a project.');
                return;
            }
            
            // Get valid assignee IDs for this project
            $validAssigneeIds = $project->assignees()->pluck('id')->toArray();
            
            // Check if all provided assignee IDs exist in this project
            $invalidAssigneeIds = array_diff($this->assignees, $validAssigneeIds);
            if (!empty($invalidAssigneeIds)) {
                $validator->errors()->add(
                    'assignees', 
                    'Some assignees do not belong to this project',
                );
            }
        });
    }
}