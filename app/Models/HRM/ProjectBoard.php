<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class ProjectBoard extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class)->orderBy('order');
    }
}
