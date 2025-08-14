<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Helpers\Transformers\LeaveTransformer;
use App\Models\HRM\Attendance;
use App\Models\HRM\Company;
use App\Models\HRM\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestApprovalController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:leave.index')->only('index');
        $this->middleware('can:leave.show')->only('show');
        $this->middleware('can:leave.approval')->only('approval');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $leaves = $this->company->leaves()
            ->with(
                'leaveDates',
                'leaveType',
                'employee.companyBranch',
                'employee.company',
                'employee.designation',
                'employee.department',
                'responder'
            )
            ->latest()
            ->paginate(25);

        $leaves->through(fn($leaveRequest) => LeaveTransformer::leaveRequest($leaveRequest));

        return response()->json([
            'data' => $leaves,
        ], 200);
    }

    public function show(Leave $leave)
    {
        $leave->load(
            'leaveDates',
            'leaveType',
            'employee.companyBranch',
            'employee.company',
            'employee.designation',
            'employee.department',
            'responder'
        );

        $leave = LeaveTransformer::leaveRequest($leave);

        return response()->json([
            'data' => $leave,
        ], 200);
    }

    public function approval(Leave $leave, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'responded_note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($leave, $request) {
            $leave->update([
                'status' => $request->status,
                'responded_by' => auth()->user()->id,
                'responded_at' => now(),
                'responded_note' => $request->responded_note,
            ]);

            if ($request->status === 'approved') {
                foreach ($leave->leaveDates as $leaveDate) {
                    Attendance::create([
                        'employee_id' => $leave->employee_id,
                        'date' => $leaveDate->date,
                        'status' => 'on_leave',
                        'approved_by' => auth()->user()->id,
                        'approved_at' => now(),
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Leave request ' . $request->status . ' successfully',
            'data' => LeaveTransformer::leaveRequest($leave->fresh()),
        ], 200);
    }

    public function cancel(Leave $leave, Request $request)
    {
        $request->validate([
            'responded_note' => 'nullable|string',
        ]);

        if ($leave->status === 'pending') {
            return response()->json([
                'message' => 'Leave request cannot be cancelled because it is pending approval. Please reject istead.',
            ], 400);
        }

        if ($leave->status === 'cancelled') {
            return response()->json([
                'message' => 'Leave request is already cancelled.',
            ], 400);
        }

        DB::transaction(function () use ($leave, $request) {
            Attendance::where('employee_id', $leave->employee_id)
                ->whereIn('date', $leave->leaveDates->pluck('date'))
                ->delete();

            $leave->update([
                'status' => 'cancelled',
                'responded_by' => auth()->user()->id,
                'responded_at' => now(),
                'responded_note' => $request->responded_note,
            ]);
        });

        return response()->json([
            'message' => 'Leave request cancelled successfully',
            'data' => LeaveTransformer::leaveRequest($leave->fresh()),
        ], 200);
    }
}
