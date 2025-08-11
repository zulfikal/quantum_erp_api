<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Attendance;
use App\Models\HRM\AttendanceBreak;

class AttendanceTransformer
{
    public static function attendance(Attendance $attendance)
    {
        return [
            'id' => $attendance->id,
            'name' => $attendance->employee->first_name . ' ' . $attendance->employee->last_name,
            'designation' => $attendance->employee->designation->name,
            'companyBranch' => $attendance->employee->companyBranch->name,
            'company' => $attendance->employee->company->name,
            'date' => $attendance->date->format('Y-m-d'),
            'clock_in_at' => $attendance->clock_in_at ? $attendance->clock_in_at->format('Y-m-d H:i:s') : null,
            'clock_out_at' => $attendance->clock_out_at ? $attendance->clock_out_at->format('Y-m-d H:i:s') : null,
            'clock_in_method' => $attendance->clock_in_method,
            'clock_out_method' => $attendance->clock_out_method,
            'clock_in_lat' => $attendance->clock_in_lat,
            'clock_in_lng' => $attendance->clock_in_lng,
            'status' => $attendance->status,
            'worked_seconds' => $attendance->worked_seconds,
            'total_break_seconds' => $attendance->total_break_seconds,
            'device_id' => $attendance->device_id,
            'notes' => $attendance->notes,
            'approved_by' => $attendance->approved_by,
            'approved_at' => $attendance->approved_at ? $attendance->approved_at->format('Y-m-d H:i:s') : null,
            'ip_address' => $attendance->ip_address,
            'created_by' => $attendance->created_by,
            'updated_by' => $attendance->updated_by,
        ];
    }

    public static function attendanceBreak(AttendanceBreak $attendanceBreak)
    {
        return [
            'id' => $attendanceBreak->id,
            'attendance_id' => $attendanceBreak->attendance_id,
            'started_at' => $attendanceBreak->started_at ? $attendanceBreak->started_at->format('Y-m-d H:i:s') : null,
            'ended_at' => $attendanceBreak->ended_at ? $attendanceBreak->ended_at->format('Y-m-d H:i:s') : null,
            'duration_seconds' => $attendanceBreak->duration_seconds,
            'notes' => $attendanceBreak->notes,
        ];
    }
}
