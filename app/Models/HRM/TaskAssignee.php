<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class TaskAssignee extends Model
{
    protected $fillable = [
        'project_task_id',
        'project_assignee_id',
    ];

    public function projectTask()
    {
        return $this->belongsTo(ProjectTask::class);
    }

    public function projectAssignee()
    {
        return $this->belongsTo(ProjectAssignee::class);
    }

    public function employee()
    {
        return $this->hasOneThrough(
            Employee::class,
            ProjectAssignee::class,
            'id', // Foreign key on ProjectAssignee table
            'id', // Foreign key on Employee table
            'project_assignee_id', // Local key on TaskAssignee table
            'employee_id' // Local key on ProjectAssignee table
        );
    }
}
