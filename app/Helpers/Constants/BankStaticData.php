<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\BankTransformer;
use App\Models\HRM\Bank;

final class BankStaticData
{
    public static function banks()
    {
        $banks = Bank::all();
        return $banks->transform(fn($bank) => BankTransformer::bank($bank));
    }
}
