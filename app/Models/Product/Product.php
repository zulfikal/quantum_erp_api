<?php

namespace App\Models\Product;

use App\Models\HRM\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'description',
        'price',
        'type',
        'stock',
        'alert_stock',
        'alert_stock_threshold',
        'is_active',
        'category_id',
    ];

    public function company() : BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
