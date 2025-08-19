<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\ProductTransformer;
use App\Models\Product\ProductCategory;
use Illuminate\Database\Eloquent\Collection;

class ProductStaticData
{
    public static function types(): array
    {
        return [
            [
                'code' => 'good',
                'name' => 'Good',
            ],
            [
                'code' => 'service',
                'name' => 'Service',
            ],
        ];
    }

    public static function categories(): Collection
    {
        return ProductCategory::all()->transform(fn ($category) => ProductTransformer::category($category));
    }
}
