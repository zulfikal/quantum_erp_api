<?php

namespace App\Helpers\Transformers;

use App\Models\Sales\CustomerReference;
use App\Models\Sales\InvoiceCustomer;

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

    public static function invoiceCustomer(InvoiceCustomer $invoiceCustomer)
    {
        return [
            'id' => $invoiceCustomer->id,
            'name' => $invoiceCustomer->name,
            'entity_id' => $invoiceCustomer->entity_id,
            'invoice_id' => $invoiceCustomer->invoice_id,
            'type' => $invoiceCustomer->type,
            'address_1' => $invoiceCustomer->address_1,
            'address_2' => $invoiceCustomer->address_2,
            'city' => $invoiceCustomer->city,
            'state' => $invoiceCustomer->state,
            'zip_code' => $invoiceCustomer->zip_code,
            'country' => $invoiceCustomer->country,
            'email' => $invoiceCustomer->email,
            'phone' => $invoiceCustomer->phone,
        ];
    }
}
