<?php

namespace App\Helpers\Constants;

class EntityStaticData
{
    public static function types(): array
    {
        return [
            'customer' => 'Customer',
            'supplier' => 'Supplier',
        ];
    }

    public static function addressTypes(): array
    {
        return [
            'billing' => 'Billing',
            'shipping' => 'Shipping',
            'billing_and_shipping' => 'Billing and Shipping',
        ];
    }

    public static function contactTypes(): array
    {
        return [
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'fax' => 'Fax',
            'other' => 'Other',
        ];
    }

    public static function status(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }
}
