<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\CompanyStaticData;
use App\Helpers\Transformers\CompanyTransformer;
use App\Helpers\Transformers\TransactionTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyBankRequest;
use App\Models\Accounting\CompanyBank;
use App\Models\HRM\Company;
use Illuminate\Http\Request;

class CompanyBankController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:company_bank.index')->only('index');
        $this->middleware('can:company_bank.create')->only('store');
        $this->middleware('can:company_bank.show')->only('show');
        $this->middleware('can:company_bank.edit')->only('update');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $companyBanks = $this->company->companyBanks()->get();
        $companyBanks->transform(fn($bank) => CompanyTransformer::bank($bank));
        return response()->json([
            'constants' => [
                'bankType' => CompanyStaticData::bankType(),
                'bankStatus' => CompanyStaticData::bankStatus(),
            ],
            'banks' => $companyBanks
        ], 200);
    }

    public function store(StoreCompanyBankRequest $request)
    {
        $companyBank = $this->company->companyBanks()->create($request->validated());

        return response()->json([
            'message' => 'Bank created successfully',
            'data' => CompanyTransformer::bank($companyBank)
        ], 201);
    }

    public function show(CompanyBank $companyBank)
    {
        if ($companyBank->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this bank.',
            ], 403);
        }

        $companyBank->load('transactions');

        $transactions = $companyBank->transactions()
            ->with('transactionMethod', 'companyBank')
            ->orderBy('date', 'desc')
            ->paginate(25);
        $transactions->through(fn($transaction) => TransactionTransformer::transaction($transaction));

        return response()->json([
            'bank' => CompanyTransformer::bank($companyBank),
            'transactions' => $transactions
        ], 200);
    }

    public function update(StoreCompanyBankRequest $request, CompanyBank $companyBank)
    {
        if ($companyBank->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this bank.',
            ], 403);
        }

        $companyBank->update($request->validated());

        return response()->json([
            'message' => 'Bank updated successfully',
            'bank' => CompanyTransformer::bank($companyBank)
        ], 200);
    }
}
