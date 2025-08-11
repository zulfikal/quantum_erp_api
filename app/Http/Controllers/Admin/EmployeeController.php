<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HRM\CompanyBranch;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\HRM\Employee;
use App\Helpers\Transformers\EmployeeTransformer;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin');
    }

    public function index(CompanyBranch $companyBranch)
    {
        $employees = $companyBranch->employees()->with('designation', 'companyBranch', 'company')->get();

        $employees->transform(fn($employee) => EmployeeTransformer::transform($employee));
        
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
        $employee->update($request->validated()['employee']);

        $employee->bankAccount()->update($request->validated()['bank']);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => EmployeeTransformer::transform($employee),
        ], 200);
    }

    public function show(Employee $employee)
    {
        
    }

    public function destroy()
    {
        
    }

}
