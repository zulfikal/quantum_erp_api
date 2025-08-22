<?php

namespace App\Models\BusinessPartner;

use Illuminate\Database\Eloquent\Model;

class EntityContact extends Model
{
    protected $fillable = [
        'entity_id',
        'type',
        'value',
        'notes'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'fax' => 'Fax',
            'other' => 'Other',
            default => 'Unknown',
        };
    }
}
