<?php

namespace App\Helpers\Transformers;

use App\Models\Sales\CustomerReference;

final class CustomerReferenceTransformer
{
    public static function customerReference(CustomerReference $customerReference)
    {
        return [
            'id' => $customerReference->id,
            'name' => $customerReference->name,
            'entity_id' => $customerReference->entity_id,
            'quotation_id' => $customerReference->quotation_id,
            'type' => $customerReference->type,
            'address_1' => $customerReference->address_1,
            'address_2' => $customerReference->address_2,
            'city' => $customerReference->city,
            'state' => $customerReference->state,
            'zip_code' => $customerReference->zip_code,
            'country' => $customerReference->country,
            'email' => $customerReference->email,
            'phone' => $customerReference->phone,
        ];
    }
}
