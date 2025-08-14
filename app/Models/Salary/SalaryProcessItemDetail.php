<?php

namespace App\Models\Salary;

use App\Models\Salary\SalaryProcessItem;
use App\Models\Salary\SalaryType;
use Illuminate\Database\Eloquent\Model;

class SalaryProcessItemDetail extends Model
{
    protected $fillable = [
        'salary_process_item_id',
        'salary_type_id',
        'amount',
    ];

    public function salaryProcessItem()
    {
        return $this->belongsTo(SalaryProcessItem::class, 'salary_process_item_id', 'id');
    }

    public function salaryType()
    {
        return $this->belongsTo(SalaryType::class, 'salary_type_id', 'id');
    }
}
