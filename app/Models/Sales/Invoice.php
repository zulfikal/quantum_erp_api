<?php

namespace App\Models\Sales;

use App\Models\HRM\Company;
use App\Models\HRM\CompanyBranch;
use App\Models\HRM\Employee;
use App\Models\Sales\SaleStatus;
use App\Models\Sales\InvoiceItem;
use App\Models\Sales\InvoiceCustomer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'employee_id',
        'description',
        'invoice_number',
        'total_amount',
        'discount_amount',
        'shipping_amount',
        'grand_total',
        'sale_status_id',
        'invoice_customer_id',
        'tax_amount',
        'invoice_date',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function saleStatus() : BelongsTo
    {
        return $this->belongsTo(SaleStatus::class);
    }

    public function company() : BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch() : BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items() : HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function invoiceCustomer() : HasOne
    {
        return $this->hasOne(InvoiceCustomer::class);
    }
}
