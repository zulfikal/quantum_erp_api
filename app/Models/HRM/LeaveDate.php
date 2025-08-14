<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class LeaveDate extends Model
{
    protected $fillable = [
        'leave_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    public function employee()
    {
        return $this->hasOneThrough(
            Employee::class,
            Leave::class,
            'id', // Foreign key on leaves table
            'id', // Foreign key on employees table
            'leave_id', // Local key on leave_dates table
            'employee_id' // Local key on leaves table
        );
    }
}
