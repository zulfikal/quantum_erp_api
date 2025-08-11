<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HRM\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\Transformers\AttendanceTransformer;
use App\Models\HRM\AttendanceBreak;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['employee.companyBranch.company', 'approver'])
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->from && $request->to, fn($q) => $q->whereBetween('date', [$request->from, $request->to]))
            ->orderByDesc('date')
            ->paginate(20);

        $transformed = $query->through(function ($attendance) {
            return AttendanceTransformer::attendance($attendance);
        });

        return response()->json($transformed);
    }

    /**
     * Clock in an employee.
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'method'      => 'required|in:web,mobile,biometric,kiosk,manual',
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
        ]);

        $nowLocal = Carbon::now(); // assume system is in employee's timezone or handle conversion

        // Check if already clocked in today
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $nowLocal->toDateString()
            ],
            [
                'clock_in_at'     => now(),
                'clock_in_method' => $request->method,
                'clock_in_lat'    => $request->lat,
                'clock_in_lng'    => $request->lng,
                'status'          => 'present',
                'ip_address'      => $request->ip(),
                'created_by'      => auth()->user()->id,
            ]
        );

        return response()->json([
            'message'    => $attendance->wasRecentlyCreated ? 'Clocked in successfully' : 'Already clocked in today',
            'attendance' => AttendanceTransformer::attendance($attendance)
        ]);
    }

    /**
     * Clock out an employee.
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'method'      => 'required|in:web,mobile,biometric,kiosk,manual',
        ]);

        $nowLocal = Carbon::now();

        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $nowLocal->toDateString())
            ->first();

        if (! $attendance) {
            return response()->json(['message' => 'No clock-in record found for today'], 404);
        }

        if ($attendance->clock_out_at) {
            return response()->json(['message' => 'Already clocked out'], 400);
        }

        DB::transaction(function () use ($attendance, $request) {
            $attendance->clock_out_at     = now();
            $attendance->clock_out_method = $request->method;
            $attendance->updated_by       = Auth::id();
            $attendance->save();
            $attendance->updateWorkedTimes();
        });

        return response()->json([
            'message'    => 'Clocked out successfully',
            'attendance' => $attendance->fresh()
        ]);
    }

    /**
     * Start a break.
     */
    public function breakStart($attendanceId, Request $request)
    {
        $attendance = Attendance::find($attendanceId);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        $attendance->breaks()->create([
            'started_at' => now(),
            'notes'      => $request->notes,
        ]);

        return response()->json(['message' => 'Break started', 'break' => AttendanceTransformer::attendanceBreak($attendance->breaks()->latest()->first())]);
    }

    /**
     * End a break.
     */
    public function breakEnd($attendanceBreakId, Request $request)
    {
        $attendanceBreak = AttendanceBreak::find($attendanceBreakId);

        if (!$attendanceBreak) {
            return response()->json(['message' => 'Attendance break not found'], 404);
        }

        if ($attendanceBreak->ended_at) {
            return response()->json(['message' => 'Break already ended'], 400);
        }

        $attendanceBreak->ended_at = now();
        $attendanceBreak->duration_seconds = $attendanceBreak->started_at->diffInSeconds($attendanceBreak->ended_at);
        $attendanceBreak->save();

        // Update parent attendance total break seconds
        $attendance = $attendanceBreak->attendance;
        $attendance->updateWorkedTimes();

        return response()->json(['message' => 'Break ended', 'break' => AttendanceTransformer::attendanceBreak($attendanceBreak)]);
    }

    /**
     * Approve an attendance correction.
     */
    public function approve($attendanceId, Request $request)
    {
        $attendance = Attendance::find($attendanceId);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        $attendance->approved_by = Auth::id();
        $attendance->approved_at = now();
        $attendance->status      = 'present';
        $attendance->save();

        return response()->json(['message' => 'Attendance approved', 'attendance' => $attendance]);
    }

    /**
     * Reject an attendance correction.
     */
    public function reject($attendanceId, Request $request)
    {
        $attendance = Attendance::find($attendanceId);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        $attendance->approved_by = Auth::id();
        $attendance->approved_at = now();
        $attendance->status      = 'absent';
        $attendance->notes       = $request->notes ?? 'Rejected by admin';
        $attendance->save();

        return response()->json(['message' => 'Attendance rejected', 'attendance' => $attendance]);
    }
}
