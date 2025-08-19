<?php

namespace App\Helpers\Constants;

use App\Models\HRM\Attendance;
use App\Models\HRM\AttendanceBreak;

final class AttendanceStaticData
{
    public static function types()
    {
        return [
            [
                'code' => 'present',
                'value' => 'Present',
            ],
            [
                'code' => 'absent',
                'value' => 'Absent',
            ],
            [
                'code' => 'on_leave',
                'value' => 'On Leave',
            ],
            [
                'code' => 'half_day',
                'value' => 'Half Day',
            ],
            [
                'code' => 'pending',
                'value' => 'Pending',
            ],
        ];
    }
}
