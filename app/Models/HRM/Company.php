<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'register_number',
        'tin_number',
    ];

    public function branches()
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
