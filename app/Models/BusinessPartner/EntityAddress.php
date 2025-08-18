<?php

namespace App\Models\BusinessPartner;

use Illuminate\Database\Eloquent\Model;

class EntityAddress extends Model
{
    protected $fillable = [
        'entity_id',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip_code',
        'country',
        'phone',
        'email',
        'notes',
        'type',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'billing' => 'Billing',
            'shipping' => 'Shipping',
            'billing_and_shipping' => 'Billing & Shipping',
        };
    }
}
