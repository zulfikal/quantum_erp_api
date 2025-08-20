<?php

namespace App\Models\Sales;

use App\Models\HRM\Company;
use App\Models\HRM\Branch;
use App\Models\HRM\CompanyBranch;
use App\Models\HRM\Employee;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'employee_id',
        'quotation_number',
        'total_amount',
        'discount_amount',
        'grand_total',
        'sale_status_id',
        'quotation_date',
        'tax_amount',
        'shipping_amount',
        'description',
        'notes',
    ];

    protected $casts = [
        'quotation_date' => 'date',
    ];

    public function saleStatus()
    {
        return $this->belongsTo(SaleStatus::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function customerReferences()
    {
        return $this->hasOne(CustomerReference::class);
    }
}
