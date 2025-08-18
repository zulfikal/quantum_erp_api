<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\EmployeeStaticData;
use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Helpers\Transformers\EmployeeTransformer;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\HRM\Employee;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $search = $request->input('search');
        $department = $request->input('department');
        $designation = $request->input('designation');
        $branch = $request->input('branch');
        $gender = $request->input('gender');
        $marital_status = $request->input('marital_status');
        $employee_status = $request->input('status');

        $designations = EmployeeStaticData::designation($this->company);
        $branches = EmployeeStaticData::branch($this->company);
        $departments = EmployeeStaticData::department($this->company);
        $genders = EmployeeStaticData::gender();
        $marital_statuses = EmployeeStaticData::maritalStatus();
        $statuses = EmployeeStaticData::status();

        $employees = $this->company->employees()
            ->when(
                $search,
                fn($query) =>
                $query->where('employees.first_name', 'like', "%{$search}%")
                    ->orWhere('employees.last_name', 'like', "%{$search}%")
                    ->orWhere('employees.nric_number', 'like', "%{$search}%")
                    ->orWhere('employees.email', 'like', "%{$search}%")
                    ->orWhere('employees.phone', 'like', "%{$search}%")
            )
            ->when(
                $department,
                fn($query) =>
                $query->whereHas('department', fn($query) => $query->where('id', $department))
            )
            ->when(
                $designation,
                fn($query) =>
                $query->whereHas('designation', fn($query) => $query->where('id', $designation))
            )
            ->when(
                $branch,
                fn($query) =>
                $query->whereHas('companyBranch', fn($query) => $query->where('id', $branch))
            )
            ->when(
                $gender,
                fn($query) =>
                $query->where('gender', $gender)
            )
            ->when(
                $marital_status,
                fn($query) =>
                $query->where('marital_status', $marital_status)
            )
            ->when(
                $employee_status,
                fn($query) =>
                $query->where('status', $employee_status)
            )
            ->with('designation', 'companyBranch', 'company', 'department', 'bankAccount.bank')->paginate(25);

        $employees = $employees->through(fn($q) => EmployeeTransformer::transform($q));

        return response()->json([
            'statistics' => [
                'total' => $this->company->employees()->count(),
                'active' => $this->company->employees()->where('status', 'active')->count(),
                'inactive' => $this->company->employees()->where('status', 'inactive')->count(),
            ],
            'constants' => [
                'designations' => $designations,
                'branches' => $branches,
                'departments' => $departments,
                'genders' => $genders,
                'marital_statuses' => $marital_statuses,
                'employee_statuses' => $statuses,
            ],
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

        if (auth()->user()->hasRole('employee') && $employee->id !== auth()->user()->employee->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

        return response()->json([
            'employee' => EmployeeTransformer::transform($employee),
        ], 200);
    }
}
