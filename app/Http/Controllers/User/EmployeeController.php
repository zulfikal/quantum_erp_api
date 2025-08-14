<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Helpers\Transformers\EmployeeTransformer;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\HRM\Employee;

class EmployeeController extends Controller
{
    private Company $company;

    public function __construct()
    {
        $this->middleware('can:employee.index')->only('index');
        $this->middleware('can:employee.create')->only('store');
        $this->middleware('can:employee.show')->only('show');
        $this->middleware('can:employee.edit')->only('update');

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
        $employees = $this->company->employees;

        $employees = $employees->transform(fn($q) => EmployeeTransformer::transform($q));

        return response()->json([
            'employees' => $employees,
        ], 200);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = Employee::create($request->validated()['employee']);

        $employee->bankAccount()->create($request->validated()['bank']);

        return response()->json([
            'message' => 'Employee stored successfully',
            'employee' => EmployeeTransformer::transform($employee),
        ], 201);
    }

    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        if ($employee->companyBranch->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

        $employee->update($request->validated()['employee']);

        $employee->bankAccount()->update($request->validated()['bank']);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => EmployeeTransformer::transform($employee),
        ], 200);
    }

    public function show(Employee $employee)
    {
        if ($employee->companyBranch->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

        if(auth()->user()->hasRole('employee') && $employee->id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

        return response()->json([
            'employee' => EmployeeTransformer::transform($employee),
        ], 200);
    }
}
