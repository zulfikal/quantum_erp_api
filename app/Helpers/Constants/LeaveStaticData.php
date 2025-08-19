<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\LeaveTransformer;
use App\Models\HRM\LeaveType;

final class LeaveStaticData
{

    public static function types()
    {
        return LeaveType::all()->transform(fn($type) => LeaveTransformer::leaveType($type));
    }

    public static function status()
    {
        return [
            ['code' => 'pending', 'value' => 'Pending'],
            ['code' => 'approved', 'value' => 'Approved'],
            ['code' => 'rejected', 'value' => 'Rejected'],
            ['code' => 'cancelled', 'value' => 'Cancelled'],
        ];
    }
}
