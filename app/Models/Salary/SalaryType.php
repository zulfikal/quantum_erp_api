<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;

class SalaryType extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function salaryItems()
    {
        return $this->hasMany(SalaryItem::class, 'salary_type_id', 'id');
    }

    public function salaryProcessItems()
    {
        return $this->hasMany(SalaryProcessItem::class, 'salary_type_id', 'id');
    }
}
