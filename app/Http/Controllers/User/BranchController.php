<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\CompanyTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyBranchRequest;
use App\Models\HRM\Company;
use App\Models\HRM\CompanyBranch;

class BranchController extends Controller
{
    private Company $company;

    public function __construct()
    {
        $this->middleware('can:company_branch.index')->only(['index']);
        $this->middleware('can:company_branch.create')->only(['store']);
        $this->middleware('can:company_branch.show')->only(['show']);
        $this->middleware('can:company_branch.edit')->only(['update']);

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
        $branches = $this->company->branches;

        $branches = $branches->transform(fn($q) => CompanyTransformer::branch($q));

        return response()->json([
            'branches' => $branches,
        ], 200);
    }

    public function show(CompanyBranch $companyBranch)
    {
        if ($companyBranch->company_id !== $this->company->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'companyBranch' => CompanyTransformer::branch($companyBranch),
        ], 200);
    }

    public function store(StoreCompanyBranchRequest $request)
    {
        $companyBranch = $this->company->branches()->create($request->validated());

        return response()->json([
            'message' => 'Company branch stored successfully',
            'companyBranch' => CompanyTransformer::branch($companyBranch),
        ], 201);
    }

    public function update(StoreCompanyBranchRequest $request, CompanyBranch $companyBranch)
    {
        if ($companyBranch->company_id !== $this->company->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $companyBranch->update($request->validated());

        return response()->json([
            'message' => 'Company branch updated successfully',
            'companyBranch' => CompanyTransformer::branch($companyBranch),
        ], 200);
    }
}
