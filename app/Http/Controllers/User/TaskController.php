<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\EmployeeTransformer;
use App\Http\Controllers\Controller;
use App\Helpers\Transformers\ProjectTransformer;
use App\Http\Requests\StoreProjectTaskRequest;
use App\Http\Requests\UpdateProjectTaskRequest;
use App\Models\HRM\ProjectBoard;
use App\Models\HRM\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Activity\LogProjectActivity;

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

            (new LogProjectActivity($projectBoard->project, 'create', "Created new task '{$request->title}'"))();

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

    public function update(ProjectTask $task, UpdateProjectTaskRequest $request)
    {
        // Store original values before update
        $originalValues = [
            'priority_id' => $task->priority_id,
            'title' => $task->title,
            'description' => $task->description,
            'start_date' => $task->start_date ? $task->start_date->format('Y-m-d') : null,
            'end_date' => $task->end_date ? $task->end_date->format('Y-m-d') : null,
            'is_completed' => $task->is_completed,
        ];

        $newValues = [
            'priority_id' => $request->priority_id,
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_completed' => (bool) $request->is_completed,
        ];

        // Check if there are any changes
        $hasChanges = false;
        $changedFields = [];

        foreach ($newValues as $field => $newValue) {
            if ($originalValues[$field] != $newValue) {
                $hasChanges = true;
                $changedFields[] = $field;
            }
        }

        // Only update and log if there are changes
        if ($hasChanges) {
            $task->update($newValues);

            $task->load('priority', 'assignees.projectAssignee.employee', 'comments.employee');

            // Create a more descriptive log message with changed fields
            $fieldNames = [
                'priority_id' => 'priority',
                'title' => 'title',
                'description' => 'description',
                'start_date' => 'start date',
                'end_date' => 'end date',
                'is_completed' => 'completion status',
            ];

            $changedFieldsExcludingCompletion = array_filter($changedFields, fn($field) => $field !== 'is_completed');
            $changedFieldNames = array_map(fn($field) => $fieldNames[$field], $changedFieldsExcludingCompletion);
            $changedFieldsText = implode(', ', $changedFieldNames);

            if (!empty($changedFieldsText)) {
                (new LogProjectActivity($task->projectBoard->project, 'update', "Updated task '{$task->title}' - changed: {$changedFieldsText}"))();
            }

            if (in_array('is_completed', $changedFields)) {
                (new LogProjectActivity($task->projectBoard->project, 'update', ($newValues['is_completed'] == true ? 'Completed' : 'Uncompleted') . " task '{$task->title}'"))();
            }
        } else {
            // Still load relationships even if no changes
            $task->load('priority', 'assignees.projectAssignee.employee', 'comments.employee');
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => ProjectTransformer::tasks($task),
        ], 200);
    }

    public function reorderTasks(ProjectBoard $fromBoard, ProjectBoard $toBoard, Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|integer:exists:project_tasks,id',
            'to_board_task_ids' => 'required|array',
            'to_board_task_ids.*' => 'required|integer:exists:project_tasks,id',
        ]);

        $isInvalid = $toBoard->tasks()->whereNotIn('id', $validated['to_board_task_ids'])->get();

        if ($isInvalid->isNotEmpty()) {
            return response()->json([
                'message' => 'Invalid task IDs provided.',
            ], 400);
        }

        DB::transaction(function () use ($validated, $fromBoard, $toBoard) {
            $task = ProjectTask::find($validated['task_id']);
            
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

            if($fromBoard->id != $toBoard->id) {
                (new LogProjectActivity($fromBoard->project, 'update', "Changed '{$task->title}' from board '{$fromBoard->title}' to board '{$toBoard->title}'"))();
            }
        });

        $boards = $fromBoard->project->boards()->with('tasks.priority', 'tasks.assignees.projectAssignee.employee', 'tasks.comments.employee')->get();

        return response()->json([
            'message' => 'Tasks reordered successfully',
            'boards' => $boards->transform(fn(ProjectBoard $board) => ProjectTransformer::boards($board)),
        ], 200);
    }

    public function show(ProjectTask $task)
    {
        $task->load('priority', 'assignees.projectAssignee.employee', 'comments.employee');

        return response()->json([
            'task' => ProjectTransformer::tasks($task),
        ], 200);
    }

    public function destroy(ProjectTask $task)
    {
        $title = $task->title;

        $task->delete();

        (new LogProjectActivity($task->projectBoard->project, 'delete', "Deleted task '{$title}'"))();

        return response()->json([
            'message' => 'Task deleted successfully',
        ], 200);
    }
}
