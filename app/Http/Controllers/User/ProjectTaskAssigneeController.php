<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Models\HRM\ProjectTask;
use App\Models\HRM\TaskAssignee;
use Illuminate\Http\Request;

class ProjectTaskAssigneeController extends Controller
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

    public function store(ProjectTask $task, Request $request)
    {
        $validated = $request->validate([
            'project_assignee_ids' => 'required|array',
            'project_assignee_ids.*' => 'required|exists:project_assignees,id',
        ]);

        $assignees = $task->assignees()->get()->pluck('project_assignee_id')->toArray();

        foreach ($validated['project_assignee_ids'] as $projectAssigneeId) {
            if (in_array($projectAssigneeId, $assignees)) {
                continue;
            }
            $task->assignees()->create([
                'project_assignee_id' => $projectAssigneeId,
            ]);
        }

        return response()->json([
            'message' => 'Employee assigned to project successfully',
        ], 201);
    }

    public function destroy(TaskAssignee $taskAssignee)
    {
        $taskAssignee->delete();

        return response()->json([
            'message' => 'Employee removed from project successfully',
        ], 200);
    }
}
