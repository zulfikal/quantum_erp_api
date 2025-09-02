<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class ProjectActivity extends Model
{
    protected $fillable = [
        'project_id',
        'employee_id',
        'action',
        'description',
    ];
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
