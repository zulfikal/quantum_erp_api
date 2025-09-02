<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Project;
use App\Models\HRM\ProjectActivity;
use App\Models\HRM\ProjectAssignee;
use App\Models\HRM\ProjectBoard;
use App\Models\HRM\ProjectPriority;
use App\Models\HRM\ProjectStatus;
use App\Models\HRM\ProjectTask;
use App\Models\HRM\TaskAssignee;
use App\Models\HRM\TaskComment;

final class ProjectTransformer
{
    public static function priority(ProjectPriority $projectPriority)
    {
        return [
            'id' => $projectPriority->id,
            'name' => $projectPriority->name,
        ];
    }

    public static function status(ProjectStatus $projectStatus)
    {
        return [
            'id' => $projectStatus->id,
            'name' => $projectStatus->name,
        ];
    }

    public static function assignees(ProjectAssignee $projectAssignee)
    {
        return [
            'id' => $projectAssignee->id,
            'employee_id' => $projectAssignee->employee_id,
            'name' => $projectAssignee->employee->full_name,
        ];
    }

    public static function taskAssignees(TaskAssignee $taskAssignee)
    {
        return [
            'id' => $taskAssignee->id,
            'employee_id' => $taskAssignee->projectAssignee->employee_id,
            'name' => $taskAssignee->projectAssignee->employee->full_name,
        ];
    }

    public static function boards(ProjectBoard $projectBoard)
    {
        return [
            'id' => $projectBoard->id,
            'title' => $projectBoard->title,
            'order' => $projectBoard->order,
            'tasks' => $projectBoard->tasks->transform(fn(ProjectTask $projectTask) => self::taskList($projectTask)),
        ];
    }

    public static function tasks(ProjectTask $projectTask)
    {
        return [
            'id' => $projectTask->id,
            'title' => $projectTask->title,
            'description' => $projectTask->description,
            'priority' => self::priority($projectTask->priority),
            'is_completed' => $projectTask->is_completed ? true : false,
            'start_date' => $projectTask->start_date ? $projectTask->start_date->format('Y-m-d') : null,
            'end_date' => $projectTask->end_date ? $projectTask->end_date->format('Y-m-d') : null,
            'comments' => $projectTask->comments ? json_decode($projectTask->comments, true) : [],
            'order' => $projectTask->order,
            'assignees' => $projectTask->assignees->transform(fn(TaskAssignee $taskAssignee) => self::taskAssignees($taskAssignee)),
        ];
    }

    public static function taskList(ProjectTask $projectTask)
    {
        return [
            'id' => $projectTask->id,
            'title' => $projectTask->title,
            'description' => $projectTask->description,
            'priority' => self::priority($projectTask->priority),
            'is_completed' => $projectTask->is_completed ? true : false,
            'start_date' => $projectTask->start_date ? $projectTask->start_date->format('Y-m-d') : null,
            'end_date' => $projectTask->end_date ? $projectTask->end_date->format('Y-m-d') : null,
            'comment_count' => $projectTask->comments()->count(),
            'order' => $projectTask->order,
            'assignees_count' => $projectTask->assignees()->count(),
            'is_your_task' => $projectTask->isYourTask(),
        ];
    }

    public static function project(Project $project)
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
            'end_date' => $project->end_date ? $project->end_date->format('Y-m-d') : null,
            'priority' => self::priority($project->priority),
            'status' => self::status($project->status),
            'department' => CompanyTransformer::department($project->department),
            'manager' => EmployeeTransformer::employee($project->manager),
            'assignees' => $project->assignees->transform(fn(ProjectAssignee $projectAssignee) => self::assignees($projectAssignee)),
        ];
    }

    public static function projects(Project $project)
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
            'end_date' => $project->end_date ? $project->end_date->format('Y-m-d') : null,
            'priority' => self::priority($project->priority),
            'status' => self::status($project->status),
            'department' => CompanyTransformer::department($project->department),
            'manager' => EmployeeTransformer::employee($project->manager),
            'progress' => $project->progressPercentage(),
            'completed_tasks' => $project->completedTasks(),
        ];
    }

    public static function activity(ProjectActivity $projectActivity)
    {
        return [
            'id' => $projectActivity->id,
            'employee' => EmployeeTransformer::employee($projectActivity->employee),
            'action' => $projectActivity->action,
            'description' => $projectActivity->description,
            'date_time' => $projectActivity->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public static function comments(TaskComment $taskComment)
    {
        return [
            'id' => $taskComment->id,
            'employee' => EmployeeTransformer::employee($taskComment->employee),
            'message' => $taskComment->message,
            'date_time' => $taskComment->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
