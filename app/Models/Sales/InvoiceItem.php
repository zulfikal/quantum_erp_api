<?php

namespace App\Models\Sales;

use App\Models\Product\Product;
use App\Models\Sales\Invoice;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'name',
        'type',
        'sku',
        'description',
        'price',
        'discount',
        'tax_percentage',
        'tax_amount',
        'quantity',
        'total',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
