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
        'is_default',
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

    public function setDefault()
    {
        // First set all other addresses to non-default
        $this->entity->addresses()
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Then set this address to default
        $this->update(['is_default' => true]);
    }

    public function getIsDefaultLabelAttribute() : bool
    {
        return $this->is_default;
    }
}
