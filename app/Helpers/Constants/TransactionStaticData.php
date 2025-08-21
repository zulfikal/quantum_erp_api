<?php

namespace App\Helpers\Constants;

use App\Helpers\Transformers\TransactionTransformer;
use App\Models\Accounting\TransactionMethod;

final class TransactionStaticData
{
    public static function types()
    {
        return [
            [
                'code' => 'debit',
                'value' => 'Debit'
            ],
            [
                'code' => 'credit',
                'value' => 'Credit'
            ]
        ];
    }

    public static function methods()
    {
        $transactionMethods = TransactionMethod::all();
        $transactionMethods->transform(fn($transactionMethod) => TransactionTransformer::transactionMethod($transactionMethod));
        return $transactionMethods;
    }
}
