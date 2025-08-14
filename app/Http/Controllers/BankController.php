<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HRM\Bank;
use App\Helpers\Transformers\BankTransformer;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::all();

        $banks->transform(function ($bank) {
            return BankTransformer::bank($bank);
        });

        return response()->json([
            'banks' => $banks
        ]);
    }
}
