<?php

namespace App\Helpers\Transformers;

class BankTransformer
{
    public static function bank($bank)
    {
        return [
            'id' => $bank->id,
            'name' => $bank->name,
            'code' => $bank->code,
        ];
    }
}