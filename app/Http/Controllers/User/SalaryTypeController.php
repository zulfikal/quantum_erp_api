<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\SalaryTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaryTypeRequest;
use App\Models\HRM\Company;
use App\Models\Salary\SalaryType;

class SalaryTypeController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:salary_type.index')->only('index');
        $this->middleware('can:salary_type.create')->only('store');
        $this->middleware('can:salary_type.edit')->only('update');
        $this->middleware('can:salary_type.show')->only('show');

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
        $salaryTypes = SalaryType::where('company_id', $this->company->id)->orWhere('company_id', null)->get();

        $salaryTypes->transform(fn($salaryType) => SalaryTransformer::salaryType($salaryType));

        return response()->json([
            'salaryTypes' => $salaryTypes,
        ], 200);
    }

    public function store(StoreSalaryTypeRequest $request)
    {
        $salaryType = $this->company->salaryTypes()->create($request->validated());

        return response()->json([
            'message' => 'Salary type stored successfully',
            'salaryType' => SalaryTransformer::salaryType($salaryType),
        ], 201);
    }

    public function update(StoreSalaryTypeRequest $request, SalaryType $salaryType)
    {
        if ($salaryType->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this salary type.',
            ], 403);
        }
        $salaryType->update($request->validated());

        return response()->json([
            'message' => 'Salary type updated successfully',
            'salaryType' => SalaryTransformer::salaryType($salaryType),
        ], 200);
    }
}
