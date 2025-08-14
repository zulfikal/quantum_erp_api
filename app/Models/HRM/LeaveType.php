<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}
