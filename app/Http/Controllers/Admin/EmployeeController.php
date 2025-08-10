<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\CompanyBranch;
use App\Models\HRM\Designation;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\HRM\Employee;
use App\Helpers\Transformers\EmployeeTransformer;

class EmployeeController extends Controller
{
    public function index(CompanyBranch $companyBranch)
    {
        $employees = $companyBranch->employees()->with('designation', 'companyBranch', 'company')->get();

        $employees->transform(fn($employee) => EmployeeTransformer::transform($employee));
        
        return response()->json([
            'employees' => $employees,
        ], 200);
    }

    public function store(StoreEmployeeRequest $request, CompanyBranch $companyBranch)
    {
        $employee = $companyBranch->employees()->create($request->validated());

        return response()->json([
            'message' => 'Employee stored successfully',
            'employee' => EmployeeTransformer::transform($employee),
        ], 201);
    }

    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->validated());

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee,
        ], 200);
    }

    public function show(Employee $employee)
    {
        
    }

    public function destroy()
    {
        
    }

}
