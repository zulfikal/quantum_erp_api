<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Helpers\Transformers\LeaveTransformer;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use App\Models\HRM\Leave;
use App\Models\HRM\LeaveDate;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = auth()->user()->employee
            ->leaves()
            ->with(
                'leaveDates',
                'leaveType',
                'employee.companyBranch',
                'employee.company',
                'employee.designation',
                'employee.department',
                'responder'
            )
            ->paginate(25);

        $leaveRequests->through(fn($leaveRequest) => LeaveTransformer::leaveRequest($leaveRequest));

        return response()->json([
            'data' => $leaveRequests,
        ], 200);
    }

    public function store(StoreLeaveRequest $request)
    {
        $date_exists = LeaveDate::whereHas('leave', function ($query) {
            $query->where('employee_id', auth()->user()->employee->id)
                ->whereIn('status', ['pending', 'approved']);
        })->whereIn('date', $request->validated()['dates'])->get();

        if ($date_exists->isNotEmpty()) {
            return response()->json([
                'message' => 'Date already exists in pending or approved leave request',
                'data' => $date_exists->map(fn($date) => LeaveTransformer::leaveDate($date)),
            ], 400);
        }

        $leaveRequest = auth()->user()->employee->leaves()->create(
            [
                'leave_type_id' => $request->validated()['leave_type_id'],
                'notes' => $request->validated()['notes'],
                'employee_id' => auth()->user()->employee->id,
                'request_date' => now(),
                'status' => 'pending',
                'responded_by' => null,
                'responded_at' => null,
                'responded_note' => null,
            ]
        );

        $leaveRequest->leaveDates()->createMany(
            array_map(fn($date) => ['date' => $date], $request->validated()['dates'])
        );

        return response()->json([
            'message' => 'Leave request created successfully',
            'data' => LeaveTransformer::leaveRequest($leaveRequest),
        ], 201);
    }

    public function show(Leave $leave)
    {
        if ($leave->employee_id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'You are not authorized to view this leave request',
            ], 401);
        }

        return response()->json([
            'data' => LeaveTransformer::leaveRequest($leave),
        ], 200);
    }

    public function updateLeave(UpdateLeaveRequest $request, Leave $leave)
    {
        if ($leave->employee_id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'You are not authorized to update this leave request',
            ], 401);
        }

        if ($leave->status !== 'pending') {
            return response()->json([
                'message' => 'Leave request cannot be updated because it is already ' . $leave->status,
            ], 400);
        }

        $leave->update($request->validated());

        return response()->json([
            'message' => 'Leave request updated successfully',
            'data' => LeaveTransformer::leaveRequest($leave->fresh()),
        ], 200);
    }

    public function deleteLeave(Leave $leave)
    {
        if ($leave->employee_id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this leave request',
            ], 401);
        }

        if ($leave->status !== 'pending') {
            return response()->json([
                'message' => 'Leave request cannot be deleted because it is already ' . $leave->status,
            ], 400);
        }

        $leave->delete();

        return response()->json([
            'message' => 'Leave request deleted successfully',
        ], 200);
    }

    public function storeLeaveDate(Leave $leave, Request $request)
    {
        if ($leave->employee_id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'You are not authorized to create this leave date',
            ], 401);
        }

        $request->validate([
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($leave) {
                    $exists = $leave->leaveDates()
                        ->whereHas('leave', function ($query) {
                            $query->where('employee_id', auth()->user()->employee->id)
                                ->whereIn('status', ['pending', 'approved']);
                        })
                        ->whereDate('date', $value)
                        ->exists();

                    if ($exists) {
                        $fail('This date is already added to this leave request.');
                    }
                },
            ],
        ], [
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a valid date',
        ]);

        if ($leave->status !== 'pending') {
            return response()->json([
                'message' => 'Leave date cannot be created because it is already ' . $leave->status,
            ], 400);
        }

        $date = $leave->leaveDates()->create([
            'date' => $request->date,
        ]);

        return response()->json([
            'message' => 'Leave date created successfully',
            'data' => LeaveTransformer::leaveDate($date),
        ], 201);
    }

    public function updateLeaveDate(Request $request, LeaveDate $leaveDate)
    {
        if ($leaveDate->leave->employee_id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'You are not authorized to update this leave date',
            ], 401);
        }

        $request->validate([
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($leaveDate) {
                    $exists = $leaveDate->leave->leaveDates()
                        ->whereHas('leave', function ($query) {
                            $query->where('employee_id', auth()->user()->employee->id)
                                ->whereIn('status', ['pending', 'approved']);
                        })
                        ->whereDate('date', $value)
                        ->exists();

                    if ($exists) {
                        $fail('This date is already added to this leave request.');
                    }
                },
            ],

        ]);

        if ($leaveDate->leave->status !== 'pending') {
            return response()->json([
                'message' => 'Leave date cannot be updated because it is already ' . $leaveDate->leave->status,
            ], 400);
        }

        $leaveDate->update([
            'date' => $request->date,
        ]);

        return response()->json([
            'message' => 'Leave date updated successfully',
            'data' => LeaveTransformer::leaveDate($leaveDate->fresh()),
        ], 200);
    }

    public function deleteLeaveDate(LeaveDate $leaveDate)
    {
        if ($leaveDate->leave->employee_id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this leave date',
            ], 401);
        }

        if ($leaveDate->leave->status !== 'pending') {
            return response()->json([
                'message' => 'Leave date cannot be deleted because it is already ' . $leaveDate->leave->status,
            ], 400);
        }

        $leaveDate->delete();

        return response()->json([
            'message' => 'Leave date deleted successfully',
        ], 200);
    }
}
