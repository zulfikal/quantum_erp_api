<?php

namespace App\Helpers\Activity;

use App\Models\HRM\Project;
use App\Models\HRM\ProjectActivity;

class LogProjectActivity
{
    private Project $project;
    private String $action;
    private String $description;

    public function __construct(Project $project, String $action, String $description)
    {
        $this->project = $project;
        $this->action = $action;
        $this->description = $description;
    }

    public function __invoke()
    {
        ProjectActivity::create([
            'project_id' => $this->project->id,
            'employee_id' => auth()->user()->employee->id,
            'action' => $this->action,
            'description' => $this->description,
        ]);
    }
}
