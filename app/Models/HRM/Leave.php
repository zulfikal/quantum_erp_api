<?php

namespace App\Models\HRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Leave extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'notes',
        'status',
        'request_date',
        'responded_by',
        'responded_at',
        'responded_note',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Get the company associated with the leave through employee and branch.
     * This creates a relationship chain: Leave -> Employee -> Branch -> Company
     */
    public function company(): HasOneThrough
    {
        return $this->hasOneThrough(
            Company::class,
            Employee::class,
            'id', // Foreign key on the intermediate table (employees)
            'id', // Foreign key on the final table (companies)
            'employee_id', // Local key on this model (leaves)
            'company_branch_id' // Second local key on the intermediate table (employees)
        );
    }

    public function leaveDates(): HasMany
    {
        return $this->hasMany(LeaveDate::class);
    }
}
