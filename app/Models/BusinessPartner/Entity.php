<?php

namespace App\Models\BusinessPartner;

use App\Models\HRM\Company;
use App\Models\HRM\Employee;
use App\Models\IdentityType;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = [
        'company_id',
        'created_by',
        'name',
        'type',
        'status',
        'entity_id',
        'tin_number',
        'identity_type_id',
        'identity_number',
        'sst_number',
        'website',
        'notes',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function contacts()
    {
        return $this->hasMany(EntityContact::class);
    }

    public function addresses()
    {
        return $this->hasMany(EntityAddress::class);
    }

    public function identityType()
    {
        return $this->belongsTo(IdentityType::class);
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'customer' => 'Customer',
            'supplier' => 'Supplier',
            default => 'Unknown',
        };
    }
}
