<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\Company;
use App\Helpers\Transformers\CompanyBranchTransformer;
use App\Http\Requests\StoreCompanyBranchRequest;
use App\Models\HRM\CompanyBranch;

class CompanyBranchController extends Controller
{
    public function index(Company $company)
    {
        $companyBranches = $company->branches;

        $companyBranches = $companyBranches->transform(fn($q) => CompanyBranchTransformer::transform($q));

        return response()->json([
            'branches' => $companyBranches,
        ]);
    }

    public function store(StoreCompanyBranchRequest $request, Company $company)
    {
        $companyBranch = $company->branches()->create($request->validated());

        return response()->json([
            'message' => 'Company branch stored successfully',
            'companyBranch' => CompanyBranchTransformer::transform($companyBranch),
        ], 201);
    }

    public function update(StoreCompanyBranchRequest $request, CompanyBranch $companyBranch)
    {
        $companyBranch->update($request->validated());

        return response()->json([
            'message' => 'Company branch updated successfully',
            'companyBranch' => CompanyBranchTransformer::transform($companyBranch),
        ], 200);
    }
}
