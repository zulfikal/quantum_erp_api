<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'company_id',
        'status',
        'name',
        'code',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
