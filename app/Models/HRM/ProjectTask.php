<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    protected $fillable = [
        'project_board_id',
        'priority_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'is_completed',
        'order',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function projectBoard()
    {
        return $this->belongsTo(ProjectBoard::class);
    }

    public function priority()
    {
        return $this->belongsTo(ProjectPriority::class);
    }

    public function assignees()
    {
        return $this->hasMany(TaskAssignee::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(
            Employee::class,
            TaskAssignee::class,
            'project_task_id', // Foreign key on TaskAssignee table
            'id', // Foreign key on Employee table
            'id', // Local key on ProjectTask table
            'employee_id' // Local key on TaskAssignee table
        );
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function isYourTask()
    {
        return $this->assignees()->whereHas('projectAssignee.employee', function ($query) {
            $query->where('id', auth()->user()->employee->id);
        })->exists();
    }
}
