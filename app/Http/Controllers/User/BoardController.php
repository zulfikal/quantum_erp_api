<?php

namespace App\Http\Controllers\User;

use App\Helpers\Activity\LogProjectActivity;
use App\Http\Controllers\Controller;
use App\Models\HRM\Project;
use App\Models\HRM\ProjectBoard;
use App\Helpers\Transformers\ProjectTransformer;
use App\Models\HRM\Company;
use App\Models\HRM\ProjectAssignee;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    private Company $company;

    public function __construct()
    {
        $this->middleware('can:project.index')->only('index');
        $this->middleware('can:project.create')->only('store');
        $this->middleware('can:project.show')->only('show');
        $this->middleware('can:project.edit')->only('update');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index(Project $project)
    {
        if ($project->company_id != $this->company->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this project.',
            ], 401);
        }

        $boards = $project->boards()->with('tasks.priority', 'tasks.assignees.projectAssignee.employee', 'tasks.comments.employee')->get();
        $assignees = $project->assignees()->with('employee')->get();

        return response()->json([
            'assignees' => $assignees->transform(fn(ProjectAssignee $assignee) => ProjectTransformer::assignees($assignee)),
            'boards' => $boards->transform(fn(ProjectBoard $board) => ProjectTransformer::boards($board)),
        ]);
    }

    public function store(Project $project, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
        ]);

        $board = $project->boards()->create([
            'title' => $validated['title'],
            'order' => $project->boards()->max('order') + 1,
        ]);

        // Log the board creation activity
        (new LogProjectActivity($project, 'create', "Created project board '{$validated['title']}'"))();

        return response()->json([
            'message' => 'Board created successfully',
            'board' => ProjectTransformer::boards($board),
        ], 201);
    }

    public function update(ProjectBoard $board, Request $request)
    {
        $oldTitle = $board->title;

        $validated = $request->validate([
            'title' => 'required|string',
        ]);

        $board->update([
            'title' => $validated['title'],
        ]);

        // Log the board update activity
        (new LogProjectActivity($board->project, 'update', "Updated project board from '{$oldTitle}' to '{$validated['title']}'"))();

        return response()->json([
            'message' => 'Board updated successfully',
            'board' => ProjectTransformer::boards($board),
        ], 200);
    }

    public function reorderBoards(Project $project, Request $request)
    {
        $validated = $request->validate([
            'boards_ids' => 'required|array',
            'boards_ids.*' => 'required|integer',
        ]);

        // Check if the boards_ids are associated with the project
        $isInvalid = $project->boards()->whereNotIn('id', $validated['boards_ids'])->get();

        if ($isInvalid->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid board IDs provided.',
            ], 400);
        }

        foreach ($validated['boards_ids'] as $index => $boardId) {
            ProjectBoard::find($boardId)->update(['order' => $index + 1]);
        }

        $boards = $project->boards()->with('tasks.priority', 'tasks.assignees.employee', 'tasks.comments.employee')->get();

        return response()->json([
            'success' => true,
            'message' => 'Boards reordered successfully',
            'boards' => $boards->transform(fn(ProjectBoard $board) => ProjectTransformer::boards($board)),
        ], 200);
    }
}
