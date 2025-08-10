<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'company_id',
        'name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
