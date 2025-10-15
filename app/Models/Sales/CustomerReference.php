<?php

namespace App\Models\Sales;

use App\Models\BusinessPartner\Entity;
use App\Models\HRM\Company;
use App\Models\IdentityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class CustomerReference extends Model
{
    protected $fillable = [
        'quotation_id',
        'entity_id',
        'identity_type_id',
        'identity_number',
        'name',
        'type',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip_code',
        'country',
        'email',
        'phone',
    ];

    /**
     * Get the quotation that this customer reference belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the entity that this customer reference belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the company through the quotation relationship
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function company(): HasOneThrough
    {
        return $this->hasOneThrough(
            Company::class,
            Quotation::class,
            'id', // Foreign key on the intermediate table (Quotation)
            'id', // Foreign key on the final table (Company)
            'quotation_id', // Local key on this model (CustomerReference)
            'company_id' // Local key on the intermediate table (Quotation)
        );
    }

    public function identityType(): BelongsTo
    {
        return $this->belongsTo(IdentityType::class);
    }
}
