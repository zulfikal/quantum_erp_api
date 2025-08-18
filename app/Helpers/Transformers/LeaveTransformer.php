<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Leave;
use App\Models\HRM\LeaveDate;
use App\Models\HRM\LeaveType;

class LeaveTransformer
{
    public static function leaveType(LeaveType $leaveType)
    {
        return [
            'id' => $leaveType->id,
            'name' => $leaveType->name,
        ];
    }

    public static function leaveDate(LeaveDate $leaveDate)
    {
        return [
            'id' => $leaveDate->id,
            'date' => $leaveDate->date->format('Y-m-d'),
        ];
    }

    public static function leaveRequest(Leave $leave)
    {
        return [
            'id' => $leave->id,
            'employee_id' => $leave->employee_id,
            'employee_name' => $leave->employee->full_name,
            'company' => $leave->employee->company->name,
            'branch' => $leave->employee->companyBranch->name,
            'designation' => $leave->employee->designation->name,
            'department' => $leave->employee->department->name,
            'leave_type' => self::leaveType($leave->leaveType),
            'request_at' => $leave->request_date ? $leave->request_date->format('Y-m-d') : null,
            'notes' => $leave->notes,
            'status' => $leave->status,
            'responded_by' => $leave->responded_by ? UserTransformer::transform($leave->responder) : null,
            'responded_at' => $leave->responded_at ? $leave->responded_at->format('Y-m-d') : null,
            'responded_note' => $leave->responded_note,
            'leave_dates' => $leave->leaveDates->transform(function (LeaveDate $leaveDate) {
                return self::leaveDate($leaveDate);
            }),
        ];
    }
}
