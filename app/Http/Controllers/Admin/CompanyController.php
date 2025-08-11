<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\HRM\Company;
use App\Helpers\Transformers\CompanyTransformer;
use App\Models\User;

class CompanyController extends Controller
{

    public function __construct()
    {
        // $this->middleware('can:company.index')->only('index');
        // $this->middleware('can:company.create')->only('store');
        // $this->middleware('can:company.edit')->only('update');
        // $this->middleware('can:company.show')->only('show');

        $this->middleware('role:super_admin');
    }

    public function index()
    {
        $companies = Company::all();

        return response()->json([
            'companies' => $companies->transform(fn($q) => CompanyTransformer::company($q)),
        ]);
    }

    public function show(Company $company)
    {
        return response()->json([
            'company' => CompanyTransformer::company($company),
            'branches' => $company->branches->transform(fn($q) => CompanyTransformer::branch($q)),
            'designations' => $company->designations->transform(fn($q) => CompanyTransformer::designation($q)),
        ]);
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return response()->json([
            'message' => 'Company created successfully',
            'company' => CompanyTransformer::company($company),
        ], 201);
    }

    public function update(StoreCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => CompanyTransformer::company($company),
        ], 200);
    }
}
