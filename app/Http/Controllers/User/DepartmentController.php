<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Helpers\Transformers\CompanyTransformer;
use App\Http\Requests\StoreDepartmentRequest;
use App\Models\HRM\Company;
use App\Models\HRM\Department;

class DepartmentController extends Controller
{
    private Company $company;

    public function __construct()
    {
        $this->middleware('can:department.index')->only('index');
        $this->middleware('can:department.create')->only('store');
        $this->middleware('can:department.show')->only('show');
        $this->middleware('can:department.edit')->only('update');

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
        $departments = $this->company->departments;

        $departments = $departments->transform(fn($q) => CompanyTransformer::department($q));

        return response()->json([
            'departments' => $departments,
        ], 200);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = $this->company->departments()->create($request->validated());

        return response()->json([
            'message' => 'Department stored successfully',
            'department' => CompanyTransformer::department($department),
        ], 201);
    }

    public function update(StoreDepartmentRequest $request, Department $department)
    {
        if ($department->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

        $department->update($request->validated());

        return response()->json([
            'message' => 'Department updated successfully',
            'department' => CompanyTransformer::department($department),
        ], 200);
    }

    public function show(Department $department)
    {
        if ($department->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

        return response()->json([
            'department' => CompanyTransformer::department($department),
        ], 200);
    }
}
