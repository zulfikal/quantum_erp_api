<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\Attendance;
use App\Helpers\Transformers\AttendanceTransformer;
use App\Models\HRM\AttendanceBreak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:employee');
    }

    public function index(Request $request)
    {
        $query = Attendance::with(['employee.companyBranch.company', 'approver'])
            ->where('employee_id', auth()->user()->employee->id)
            ->when($request->from && $request->to, fn($q) => $q->whereBetween('date', [$request->from, $request->to]))
            ->orderByDesc('date')
            ->paginate(20);

        $transformed = $query->through(function ($attendance) {
            return AttendanceTransformer::attendance($attendance);
        });

        return response()->json($transformed);
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'method'      => 'required|in:web,mobile,biometric,kiosk,manual',
            'status'      => 'required|in:present,absent,on_leave,half_day,pending',
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
        ]);

        $nowLocal = Carbon::now(); // assume system is in employee's timezone or handle conversion

        // Check if already clocked in today
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => auth()->user()->employee->id,
                'date' => $nowLocal->toDateString()
            ],
            [
                'clock_in_at'     => now(),
                'clock_in_method' => $request->method,
                'clock_in_lat'    => $request->lat,
                'clock_in_lng'    => $request->lng,
                'status'          => $request->status,
                'ip_address'      => $request->ip(),
                'created_by'      => auth()->user()->id,
            ]
        );

        return response()->json([
            'message'    => $attendance->wasRecentlyCreated ? 'Clocked in successfully' : 'Already clocked in today',
            'attendance' => AttendanceTransformer::attendance($attendance->fresh())
        ]);
    }

    public function clockOut(Attendance $attendance, Request $request)
    {
        $request->validate([
            'method'      => 'required|in:web,mobile,biometric,kiosk,manual',
        ]);

        if ($attendance->employee_id != auth()->user()->employee->id) {
            return response()->json(['message' => 'This operation is not allowed'], 403);
        }

        if ($attendance->clock_out_at) {
            return response()->json(['message' => 'Already clocked out'], 400);
        }

        DB::transaction(function () use ($attendance, $request) {
            $attendance->clock_out_at     = now();
            $attendance->clock_out_method = $request->method;
            $attendance->updated_by       = auth()->user()->id;
            $attendance->save();
            $attendance->updateWorkedTimes();
        });

        return response()->json([
            'message'    => 'Clocked out successfully',
            'attendance' => AttendanceTransformer::attendance($attendance->fresh())
        ]);
    }

    public function breakStart(Attendance $attendance, Request $request)
    {
        if ($attendance->employee_id != auth()->user()->employee->id) {
            return response()->json(['message' => 'This operation is not allowed'], 403);
        }

        $existingBreak = $attendance->breaks()->where('ended_at', null)->first();

        if ($existingBreak) {
            return response()->json(['message' => 'You already have an active break'], 400);
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
    public function breakEnd(AttendanceBreak $attendanceBreak, Request $request)
    {
        if ($attendanceBreak->attendance->employee_id != auth()->user()->employee->id) {
            return response()->json(['message' => 'This operation is not allowed'], 403);
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
}
