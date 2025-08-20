<?php

namespace App\Models\Sales;

use App\Models\HRM\Customer;
use Illuminate\Database\Eloquent\Model;

class InvoiceCustomer extends Model
{
    protected $fillable = [
        'invoice_id',
        'customer_id',
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
        return $this->belongsTo(Customer::class);
    }
}
