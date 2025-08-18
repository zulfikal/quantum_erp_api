<?php

namespace App\Helpers\Constants;

class EntityStaticData
{
    public static function types(): array
    {
        return [
            ['code' => 'customer', 'name' => 'Customer'],
            ['code' => 'supplier', 'name' => 'Supplier'],
        ];
    }

    public static function addressTypes(): array
    {
        return [
            ['code' => 'billing', 'name' => 'Billing'],
            ['code' => 'shipping', 'name' => 'Shipping'],
            ['code' => 'billing_and_shipping', 'name' => 'Billing and Shipping'],
        ];
    }

    public static function contactTypes(): array
    {
        return [
            ['code' => 'phone', 'name' => 'Phone'],
            ['code' => 'mobile', 'name' => 'Mobile'],
            ['code' => 'email', 'name' => 'Email'],
            ['code' => 'fax', 'name' => 'Fax'],
            ['code' => 'other', 'name' => 'Other'],
        ];
    }

    public static function status(): array
    {
        return [
            ['code' => 'active', 'name' => 'Active'],
            ['code' => 'inactive', 'name' => 'Inactive'],
        ];
    }
}
