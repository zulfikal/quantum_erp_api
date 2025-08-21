<?php

namespace App\Models\Accounting;

use App\Models\HRM\Company;
use App\Models\Sales\Invoice;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'company_id',
        'transaction_method_id',
        'invoice_id',
        'company_bank_id',
        'type',
        'amount',
        'date',
        'reference',
        'notes',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transactionMethod()
    {
        return $this->belongsTo(TransactionMethod::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function companyBank()
    {
        return $this->belongsTo(CompanyBank::class);
    }
}
