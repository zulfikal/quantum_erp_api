<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Models\HRM\ProjectTask;
use Illuminate\Http\Request;

class ProjectTaskCommentController extends Controller
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
            'message' => 'required|string',
        ]);

        $task->comments()->create([
            'employee_id' => auth()->user()->employee->id,
            'message' => $validated['message'],
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
        ], 201);
    }

    public function destroy(TaskComment $taskComment)
    {
        $taskComment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ], 200);
    }
}
