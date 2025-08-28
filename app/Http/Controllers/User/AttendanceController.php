<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\AttendanceStaticData;
use App\Helpers\Constants\CompanyStaticData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\Attendance;
use App\Helpers\Transformers\AttendanceTransformer;
use App\Models\HRM\AttendanceBreak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\HRM\Company;

class AttendanceController extends Controller
{
    private Company $company;

    public function __construct()
    {
        $this->middleware('role:employee|admin');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $type = $request->type;
        $branch = $request->branch_id;
        $department = $request->department_id;
        $employee = $request->employee;
        $from = $request->from;
        $to = $request->to;

        if (auth()->user()->hasRole('employee')) {
            $query = Attendance::with(['employee.companyBranch.company', 'employee.department', 'employee.designation', 'approver'])
                ->where('employee_id', auth()->user()->employee->id)
                ->when($from && $to, fn($q) => $q->whereDate('date', '>=', $from)->whereDate('date', '<=', $to))
                ->when($from && $to == null, fn($q) => $q->whereDate('date', '>=', $from))
                ->when($from == null && $to, fn($q) => $q->whereDate('date', '<=', $to))
                ->when($type, fn($q) => $q->where('status', $type))
                ->orderByDesc('date')
                ->paginate(20);
        }

        if (auth()->user()->hasRole('admin')) {
            $query = Attendance::with(['employee.companyBranch.company', 'employee.department', 'employee.designation', 'approver'])
                ->whereHas('employee.companyBranch.company', fn($q) => $q->where('company_id', $this->company->id))
                ->when($from && $to, fn($q) => $q->whereDate('date', '>=', $from)->whereDate('date', '<=', $to))
                ->when($from && $to == null, fn($q) => $q->whereDate('date', '>=', $from))
                ->when($from == null && $to, fn($q) => $q->whereDate('date', '<=', $to))
                ->when($type, fn($q) => $q->where('status', $type))
                ->when($branch, fn($q) => $q->whereHas('employee.companyBranch', fn($q) => $q->where('company_branch_id', $branch)))
                ->when($department, fn($q) => $q->whereHas('employee.department', fn($q) => $q->where('department_id', $department)))
                ->when($employee, fn($q) => $q->whereHas('employee', fn($q) => $q->where('first_name', 'like', '%' . $employee . '%')->orWhere('last_name', 'like', '%' . $employee . '%')))
                ->orderByDesc('date')
                ->paginate(20);
        }

        $transformed = $query->through(fn($attendance) => AttendanceTransformer::attendance($attendance));

        $today = auth()->user()->employee->attendances()->whereDate('date', now()->toDateString())->first();

        return response()->json([
            'today' => [
                'is_exists' => $today ? true : false,
                'clock_in_at' => $today->clock_in_at ? $today->clock_in_at->format('h:i A') : null,
                'clock_out_at' => $today->clock_out_at ? $today->clock_out_at->format('h:i A') : null,
                'worked_hours' => $today ? $today->worked_hours : null,
                'break_hours' => $today ? $today->break_hours : null,
                'breaks' => $today ? $today->breaks->transform(fn($break) => AttendanceTransformer::attendanceBreak($break)) : [],
            ],
            'constants' => [
                'types' => AttendanceStaticData::types(),
                'departments' => $this->company->departments->transform(fn($q) => CompanyStaticData::department($q)),
                'branches' => $this->company->branches->transform(fn($q) => CompanyStaticData::branchList($q)),
            ],
            'attendances' => $transformed
        ]);
    }

    public function show(Attendance $attendance)
    {
        if (auth()->user()->hasRole('employee') && $attendance->employee_id != auth()->user()->employee->id) {
            return response()->json(['message' => 'This operation is not allowed'], 403);
        }

        if (auth()->user()->hasRole('admin') && $attendance->employee->companyBranch->company_id != $this->company->id) {
            return response()->json(['message' => 'This operation is not allowed'], 403);
        }

        return response()->json([
            'attendance' => AttendanceTransformer::attendance($attendance),
            'breaks' => $attendance->breaks->transform(fn($break) => AttendanceTransformer::attendanceBreak($break))
        ]);
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
