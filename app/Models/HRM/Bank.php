<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function employeeBank()
    {
        return $this->hasOne(EmployeeBank::class);
    }
}
