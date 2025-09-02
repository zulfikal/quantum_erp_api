<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Models\HRM\Project;
use App\Models\HRM\ProjectAssignee;
use Illuminate\Http\Request;

class ProjectAssigneeController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:project.create')->only(['store']);
        $this->middleware('can:project.destroy')->only(['destroy']);

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function store(Project $project, Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'required|exists:employees,id',
        ]);

        $assignees = $project->assignees()->get()->pluck('employee_id')->toArray();

        foreach ($validated['employee_ids'] as $employeeId) {
            if (in_array($employeeId, $assignees)) {
                continue;
            }
            $project->assignees()->create([
                'employee_id' => $employeeId,
            ]);
        }

        return response()->json([
            'message' => 'Employee assigned to project successfully',
        ], 201);
    }

    public function destroy(ProjectAssignee $projectAssignee)
    {
        $projectAssignee->delete();

        return response()->json([
            'message' => 'Employee removed from project successfully',
        ], 200);
    }
}
