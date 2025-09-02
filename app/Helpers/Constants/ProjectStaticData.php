<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\ProjectTransformer;
use App\Models\HRM\ProjectPriority;
use App\Models\HRM\ProjectStatus;

final class ProjectStaticData
{
    public static function priorities()
    {
        $priorities = ProjectPriority::all();

        return $priorities->transform(fn($priority) => ProjectTransformer::priority($priority));
    }

    public static function statuses()
    {
        $statuses = ProjectStatus::all();

        return $statuses->transform(fn($status) => ProjectTransformer::status($status));
    }
}
