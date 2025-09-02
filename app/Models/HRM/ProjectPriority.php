<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class ProjectPriority extends Model
{
    protected $fillable = [
        'name',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
