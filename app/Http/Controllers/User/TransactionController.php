<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\TransactionStaticData;
use App\Helpers\Transformers\TransactionTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\HRM\Company;
use App\Models\Sales\Invoice;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:transaction.index')->only('index');
        $this->middleware('can:transaction.create')->only('store');
        $this->middleware('can:transaction.show')->only('show');
        $this->middleware('can:transaction.edit')->only('update');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type');
        $method_id = $request->input('method_id');
        $companyBankId = $request->input('company_bank_id');

        $transactions = $this->company->transactions()
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('date', '>=', $startDate . ' 00:00:00');
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('date', '<=', $endDate . ' 23:59:59');
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when($method_id, function ($query) use ($method_id) {
                return $query->where('transaction_method_id', $method_id);
            })
            ->when($companyBankId, function ($query) use ($companyBankId) {
                return $query->where('company_bank_id', $companyBankId);
            })
            ->with('companyBank', 'transactionMethod')
            ->paginate(25);

        $companyBanks = $this->company->companyBanks()->with('bank')->get();
        $companyBanks->transform(fn($companyBank) => [
            'id' => $companyBank->id,
            'bank' => $companyBank->bank->name,
        ]);

        $transactions->through(fn($transaction) => TransactionTransformer::transaction($transaction));
        return response()->json([
            'constants' => [
                'types' => TransactionStaticData::types(),
                'methods' => TransactionStaticData::methods(),
                'company_banks' => $companyBanks
            ],
            'transactions' => $transactions
        ], 200);
    }

    public function store(StoreTransactionRequest $request)
    {
        if ($request->invoice_id) {
            $invoice = Invoice::find($request->invoice_id);
            if (!$invoice) {
                return response()->json([
                    'message' => 'Invoice not found',
                ], 404);
            }

            if ($invoice->company_id != $this->company->id) {
                return response()->json([
                    'message' => 'You are not authorized to create a transaction for this invoice',
                ], 403);
            }
        }

        $transaction = $this->company->transactions()->create($request->validated());

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => TransactionTransformer::transaction($transaction->fresh())
        ], 201);
    }
}
