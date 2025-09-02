<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\EmployeeTransformer;
use App\Http\Controllers\Controller;
use App\Helpers\Transformers\ProjectTransformer;
use App\Http\Requests\StoreProjectTaskRequest;
use App\Models\HRM\Employee;
use App\Models\HRM\ProjectAssignee;
use App\Models\HRM\ProjectBoard;
use App\Models\HRM\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(ProjectBoard $projectBoard, StoreProjectTaskRequest $request)
    {
        $task = null;

        // Create the task and attach assignees in a transaction
        try {
            $taskId = null;

            DB::transaction(function () use ($projectBoard, $request, &$taskId) {
                $newTask = $projectBoard->tasks()->create([
                    'priority_id' => $request->priority_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_completed' => $request->is_completed,
                    'order' => $projectBoard->tasks()->max('order') + 1,
                ]);

                $taskId = $newTask->id;

                // Only create assignees if they exist
                if (!empty($request->assignees)) {
                    foreach ($request->assignees as $projectAssigneeId) {
                        $newTask->assignees()->create([
                            'project_assignee_id' => $projectAssigneeId
                        ]);
                    }
                }
            });

            // Reload the task with relationships after transaction completes
            $task = ProjectTask::with(['assignees.projectAssignee.employee', 'priority'])->findOrFail($taskId);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create task: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Task created successfully',
            'task' => ProjectTransformer::tasks($task),
        ], 201);
    }

    public function reorderTasks(ProjectBoard $fromBoard, ProjectBoard $toBoard, Request $request)
    {
        $validated = $request->validate([
            'to_board_task_ids' => 'required|array',
            'to_board_task_ids.*' => 'required|integer:exists:project_tasks,id',
        ]);

        $isInvalid = $toBoard->tasks()->whereNotIn('id', $validated['to_board_task_ids'])->get();

        if ($isInvalid->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid task IDs provided.',
            ], 400);
        }

        DB::transaction(function () use ($validated, $fromBoard, $toBoard) {
            foreach ($validated['to_board_task_ids'] as $index => $taskId) {
                ProjectTask::find($taskId)->update([
                    'project_board_id' => $toBoard->id,
                    'order' => $index + 1
                ]);
            }

            if ($fromBoard->id != $toBoard->id) {
                $fromBoardTasks = $fromBoard->tasks()->get();

                foreach ($fromBoardTasks as $index => $task) {
                    $task->update(['order' => $index + 1]);
                }
            }
        });

        $boards = $fromBoard->project->boards()->with('tasks.priority', 'tasks.assignees.projectAssignee.employee', 'tasks.comments.employee')->get();

        return response()->json([
            'success' => true,
            'message' => 'Tasks reordered successfully',
            'boards' => $boards->transform(fn(ProjectBoard $board) => ProjectTransformer::boards($board)),
        ], 200);
    }
}
