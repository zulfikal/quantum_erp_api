<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $fillable = [
        'project_task_id',
        'employee_id',
        'message',
    ];

    public function projectTask()
    {
        return $this->belongsTo(ProjectTask::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
