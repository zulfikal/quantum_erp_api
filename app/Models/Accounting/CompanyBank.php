<?php

namespace App\Models\Accounting;

use App\Models\HRM\Company;
use App\Models\HRM\Bank as HRMBank;
use Illuminate\Database\Eloquent\Model;

class CompanyBank extends Model
{
    protected $fillable = [
        'company_id',
        'bank_id',
        'account_number',
        'holder_name',
        'type',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bank()
    {
        return $this->belongsTo(HRMBank::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
