<?php

namespace App\Helpers\Transformers;

use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionMethod;

class TransactionTransformer
{
    public static function transactionMethod(TransactionMethod $transactionMethod)
    {
        return [
            'id' => $transactionMethod->id,
            'name' => $transactionMethod->name,
        ];
    }
    public static function transaction(Transaction $transaction)
    {
        return [
            'id' => $transaction->id,
            'company_id' => $transaction->company_id,
            'transaction_method' => self::transactionMethod($transaction->transactionMethod),
            'invoice_id' => $transaction->invoice_id,
            'company_bank' => $transaction->companyBank->bank->name,
            'type' => $transaction->type,
            'date' => $transaction->date->format('Y-m-d H:i:s'),
            'reference' => $transaction->reference,
            'notes' => $transaction->notes,
            'amount' => $transaction->amount,
        ];
    }
}