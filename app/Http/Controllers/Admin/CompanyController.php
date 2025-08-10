<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\HRM\Company;
use App\Helpers\Transformers\CompanyTransformer;
use App\Models\User;

class CompanyController extends Controller
{

    public function index()
    {
        $companies = Company::all();

        return response()->json([
            'companies' => $companies->transform(fn($q) => CompanyTransformer::transform($q)),
        ]);
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return response()->json([
            'message' => 'Company created successfully',
            'company' => CompanyTransformer::transform($company),
        ], 201);
    }

    public function update(StoreCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => CompanyTransformer::transform($company),
        ], 200);
    }

    public function createuser()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@quantumhrm.com',
            'password' => bcrypt('password'),
        ]);

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }
}
