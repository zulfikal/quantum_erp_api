<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    protected $fillable = [
        'company_id',
        'department_id',
        'manager_id',
        'priority_id',
        'status_id',
        'name',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function priority()
    {
        return $this->belongsTo(ProjectPriority::class);
    }

    public function status()
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function boards()
    {
        return $this->hasMany(ProjectBoard::class)->orderBy('order');
    }

    public function assignees()
    {
        return $this->hasMany(ProjectAssignee::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, ProjectAssignee::class, 'project_id', 'id', 'id', 'employee_id');
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            ProjectTask::class,
            ProjectBoard::class,
            'project_id', // Foreign key on ProjectBoard table
            'project_board_id', // Foreign key on ProjectTask table
            'id', // Local key on Project table
            'id' // Local key on ProjectBoard table
        );
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class);
    }

    /**
     * Get task completion counts
     * 
     * @return object with 'completed' and 'total' properties
     */
    private function getTaskCompletionCounts()
    {
        // Using a raw query to avoid the GROUP BY issue with only_full_group_by SQL mode
        $projectId = $this->id;
        
        try {
            $result = DB::select(
                "SELECT 
                    COUNT(*) as total, 
                    SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed 
                FROM project_tasks 
                INNER JOIN project_boards ON project_boards.id = project_tasks.project_board_id 
                WHERE project_boards.project_id = ?", 
                [$projectId]
            );
            
            // Ensure we have a result and that completed is not NULL (which can happen if there are no tasks)
            if (!empty($result) && isset($result[0])) {
                $counts = $result[0];
                // Convert to int to ensure consistent types
                return (object)[
                    'total' => (int) $counts->total,
                    'completed' => (int) ($counts->completed ?? 0)
                ];
            }
        } catch (\Exception $e) {
            // Log the error but don't crash the application
            \Log::error('Error calculating task completion counts: ' . $e->getMessage());
        }
        
        // Default return if query fails or no results
        return (object)['total' => 0, 'completed' => 0];
    }
    
    /**
     * Get formatted task completion status
     * 
     * @return string
     */
    public function completedTasks()
    {
        $counts = $this->getTaskCompletionCounts();
        return $counts->completed . ' / ' . $counts->total . ' completed';
    }

    /**
     * Get task completion percentage
     * 
     * @return float
     */
    public function progressPercentage()
    {
        $counts = $this->getTaskCompletionCounts();
        return $counts->total > 0 ? number_format($counts->completed / $counts->total * 100, 2) : "0.00";
    }
}
