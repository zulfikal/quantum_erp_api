<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\InvoiceTransformer;
use App\Models\Sales\SaleStatus;

final class InvoiceStaticData
{
    public static function statuses()
    {
        $statuses = SaleStatus::whereIn('type', ['invoice', 'quotation_invoice'])->get();

        return $statuses->transform(fn($status) => InvoiceTransformer::saleStatus($status));
    }
}
