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
        'is_default',
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

    public function setDefault()
    {
        // First set all other addresses to non-default
        $this->company->companyBanks()
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Then set this address to default
        $this->update(['is_default' => true]);
    }

    public function getIsDefaultLabelAttribute(): bool
    {
        return $this->is_default;
    }
}
