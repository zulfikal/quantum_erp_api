<?php

namespace App\Models\Sales;

use App\Models\HRM\Entity;
use App\Models\IdentityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceCustomer extends Model
{
    protected $fillable = [
        'invoice_id',
        'entity_id',
        'identity_type_id',
        'identity_number',
        'sst_number',
        'tin_number',
        'name',
        'email',
        'phone',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'id');
    }

    public function identityType(): BelongsTo
    {
        return $this->belongsTo(IdentityType::class);
    }
}
