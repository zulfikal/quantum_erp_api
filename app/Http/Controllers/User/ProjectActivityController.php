<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Project;
use App\Models\HRM\ProjectActivity;
use App\Helpers\Transformers\ProjectTransformer;

class ProjectActivityController extends Controller
{
    public function index(Project $project)
    {
        $activities = $project->activities()->with('employee')->latest()->get();

        return response()->json([
            'activities' => $activities->transform(fn(ProjectActivity $activity) => ProjectTransformer::activity($activity)),
        ]);
    }
}
