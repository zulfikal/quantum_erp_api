<?php

namespace App\Helpers\Transformers;

use App\Models\Product\Product;
use App\Models\Product\ProductCategory;

class ProductTransformer
{
    public static function category(ProductCategory $category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ];
    }

    public static function product(Product $product)
    {
        return [
            'id' => $product->id,
            'category' => self::category($product->category),
            'name' => $product->name,
            'type' => $product->type,
            'sku' => $product->sku,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'alert_stock' => $product->alert_stock,
            'alert_stock_threshold' => $product->alert_stock_threshold,
            'is_active' => $product->is_active,
        ];
    }
}
