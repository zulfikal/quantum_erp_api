<?php

namespace App\Models\HRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'employee_id',
        'claim_type_id',
        'request_date',
        'amount',
        'status',
        'description',
        'responded_by',
        'responded_at',
        'responded_note',
        'approved_amount',
    ];

    protected $casts = [
        'request_date' => 'date',
        'responded_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function claimType()
    {
        return $this->belongsTo(ClaimType::class, 'claim_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'responded_by', 'id');
    }
}
