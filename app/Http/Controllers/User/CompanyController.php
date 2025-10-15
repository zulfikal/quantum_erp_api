<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use Illuminate\Http\Request;
use App\Helpers\Transformers\CompanyTransformer;
use App\Http\Requests\StoreCompanyRequest;

class CompanyController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:company.index')->only('index');
        $this->middleware('can:company.create')->only('store');
        $this->middleware('can:company.show')->only('show');
        // $this->middleware('can:company.edit')->only('update');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function show()
    {
        $companyBranch = auth()->user()->employee->companyBranch;
        return response()->json([
            'company' => CompanyTransformer::company($this->company),
            'companyBranch' => CompanyTransformer::branch($companyBranch)
        ]);
    }

    public function update(StoreCompanyRequest $request)
    {
        $this->company->update($request->validated());

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => CompanyTransformer::company($this->company),
        ], 200);
    }
}
