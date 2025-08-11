<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class AttendanceBreak extends Model
{
    protected $fillable = [
        'attendance_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
