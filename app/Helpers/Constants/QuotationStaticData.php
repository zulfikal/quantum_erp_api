<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\QuotationTransformer;
use App\Models\Sales\SaleStatus;

final class QuotationStaticData
{
    public static function statuses()
    {
        $statuses = SaleStatus::whereIn('type', ['quotation', 'quotation_invoice'])->get();

        return $statuses->transform(fn($status) => QuotationTransformer::saleStatus($status));
    }
}
