<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Project;
use App\Models\HRM\ProjectActivity;
use App\Helpers\Transformers\ProjectTransformer;
use Illuminate\Http\Request;

class ProjectActivityController extends Controller
{
    public function index(Project $project, Request $request)
    {
        $search = $request->query('search');

        $activities = $project->activities()
            ->when($search, function ($query) use ($search) {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->with('employee')->latest()
            ->paginate(25);

        return response()->json([
            'activities' => $activities->through(fn(ProjectActivity $activity) => ProjectTransformer::activity($activity)),
        ]);
    }
}
