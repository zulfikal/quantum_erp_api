<?php

namespace App\Models\HRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'shift_id',
        'clock_in_at',
        'clock_out_at',
        'clock_in_method',
        'clock_out_method',
        'clock_in_lat',
        'clock_in_lng',
        'status',
        'worked_seconds',
        'total_break_seconds',
        'device_id',
        'notes',
        'approved_by',
        'approved_at',
        'ip_address',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeForEmployee($q, $employeeId)
    {
        return $q->where('employee_id', $employeeId);
    }

    public function scopeBetweenDates($q, $from, $to)
    {
        return $q->whereBetween('date', [$from, $to]);
    }

    public function scopePresent($q)
    {
        return $q->where('status', 'present');
    }

    public function scopeAbsent($q)
    {
        return $q->where('status', 'absent');
    }

    // Helpers
    public function computeWorkedSeconds()
    {
        if (! $this->clock_in_at || ! $this->clock_out_at) return 0;

        $seconds = $this->clock_in_at->diffInSeconds($this->clock_out_at);

        // subtract breaks if stored
        $breakSeconds = $this->breaks()->sum('duration_seconds') ?: $this->total_break_seconds ?? 0;

        return max(0, $seconds - $breakSeconds);
    }

    public function updateWorkedTimes()
    {
        $this->total_break_seconds = $this->breaks()->sum('duration_seconds') ?: $this->total_break_seconds;
        $this->worked_seconds = $this->computeWorkedSeconds();
        $this->saveQuietly(); // avoid recursion if you have observers
    }

    // Format worked time: minutes if < 1 hour, hours otherwise
    public function getWorkedHoursAttribute()
    {
        
        if ($this->clock_in_at != null && $this->clock_out_at == null) {
            $seconds = $this->clock_in_at->diffInSeconds(now());
        } else {
            $seconds = $this->worked_seconds ?? 0;
        }

        if ($seconds < 3600) {
            // Less than an hour, return minutes
            return round($seconds / 60, 0) . ' min';
        } else {
            // More than an hour, return hours
            return round($seconds / 3600, 2) . ' hr';
        }
    }

    public function getWorkedSecondsOnDemandAttribute()
    {
        
        if ($this->clock_in_at != null && $this->clock_out_at == null) {
            $seconds = $this->clock_in_at->diffInSeconds(now());
        } else {
            $seconds = $this->worked_seconds ?? 0;
        }

        return (int) $seconds;
    }

    public function getBreakHoursAttribute()
    {
        $seconds = $this->total_break_seconds ?? 0;

        if ($seconds < 3600) {
            // Less than an hour, return minutes
            return round($seconds / 60, 0) . ' min';
        } else {
            // More than an hour, return hours
            return round($seconds / 3600, 2) . ' hr';
        }
    }
}
