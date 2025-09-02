<?php

namespace App\Http\Controllers\User;

use App\Helpers\Activity\LogProjectActivity;
use App\Helpers\Constants\ProjectStaticData;
use App\Helpers\Transformers\ProjectTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\HRM\Company;
use App\Models\HRM\Project;
use App\Models\HRM\ProjectActivity;
use App\Models\HRM\ProjectAssignee;
use App\Models\HRM\ProjectBoard;
use App\Models\HRM\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
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

    public function index()
    {
        $projects = $this->company->projects()
            ->with(['priority', 'status', 'department', 'manager', 'assignees.employee'])
            ->paginate(25);

        return response()->json([
            'constants' => [
                'priorities' => ProjectStaticData::priorities(),
                'statuses' => ProjectStaticData::statuses(),
            ],
            'projects' => $projects->through(fn(Project $project) => ProjectTransformer::projects($project)),
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = DB::transaction(function () use ($validated) {
            $project = $this->company->projects()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'priority_id' => $validated['priority_id'] ?? null,
                'status_id' => $validated['status_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'manager_id' => $validated['manager_id'] ?? null,
            ]);

            // Handle assignees if provided
            if (!empty($validated['assignee_ids'])) {
                foreach ($validated['assignee_ids'] as $employeeId) {
                    $project->assignees()->create([
                        'employee_id' => $employeeId
                    ]);
                }
            }

            $project->boards()->insert([
                [
                    'project_id' => $project->id,
                    'title' => 'To Do',
                    'order' => 1,
                ],
                [
                    'project_id' => $project->id,
                    'title' => 'In Progress',
                    'order' => 2
                ],
                [
                    'project_id' => $project->id,
                    'title' => 'Done',
                    'order' => 3
                ]
            ]);

            return $project->load(['priority', 'status', 'department', 'manager', 'assignees.employee', 'boards.tasks']);
        });

        (new LogProjectActivity($project, 'create', "Created new project '{$project->name}'"))();

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'project' => ProjectTransformer::project($project),
        ], 201);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        $project->update($validated);

        $activityDescription = "Project info updated";
        (new LogProjectActivity($project, 'update', $activityDescription))();

        $project->load(['priority', 'status', 'department', 'manager', 'assignees.employee', 'boards.tasks']);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'project' => ProjectTransformer::project($project),
        ]);
    }

    public function show(Project $project)
    {
        if ($project->company_id != $this->company->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this project.',
            ], 401);
        }

        // Load everything in a single query with nested relationships
        $project->load([
            'priority',
            'status',
            'department',
            'manager',
            'assignees.employee',
            'tasks',
            'tasks.priority',
            'tasks.comments.employee',
            'tasks.assignees.projectAssignee.employee',
        ]);

        // Load activities separately (with limit)
        $activities = $project->activities()
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'project' => ProjectTransformer::project($project),
            'activities' => $activities->transform(fn(ProjectActivity $activity) => ProjectTransformer::activity($activity)),
            'tasks' => $project->tasks->transform(fn(ProjectTask $task) => ProjectTransformer::tasks($task)),
        ]);
    }
}
