<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\EmployeeStaticData;
use App\Helpers\Constants\SalaryStaticData;
use App\Helpers\Transformers\SalaryTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaryItemRequest;
use App\Models\HRM\Company;
use App\Models\HRM\Employee;
use App\Models\Salary\SalaryItem;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:salary_item.index')->only('salaryItemIndex');
        $this->middleware('can:salary_item.create')->only('salaryItemStore');
        $this->middleware('can:salary_item.edit')->only('salaryItemUpdate');
        $this->middleware('can:salary_item.destroy')->only('salaryItemDestroy');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function salaryItemIndex(Request $request)
    {
        $search = $request->input('search');
        $department = $request->input('department_id');
        $branch = $request->input('branch_id');
        $employee_status = $request->input('employee_status');

        $branches = EmployeeStaticData::branch($this->company);
        $departments = EmployeeStaticData::department($this->company);
        $statuses = EmployeeStaticData::status();
        $salaryTypes = SalaryStaticData::salaryType();

        $salaryItems = $this->company->employees()->whereHas('user', function ($query) {
            $query->withoutRole('admin');
        })
            ->when($search, function ($query) use ($search) {
                $query->where('employees.first_name', 'like', "%{$search}%")
                    ->orWhere('employees.last_name', 'like', "%{$search}%")
                    ->orWhere('employees.nric_number', "{$search}")
                    ->orWhere('employees.email', "{$search}")
                    ->orWhere('employees.phone', "{$search}");
            })
            ->when($department, function ($query) use ($department) {
                $query->whereHas('department', function ($query) use ($department) {
                    $query->where('id', $department);
                });
            })
            ->when($branch, function ($query) use ($branch) {
                $query->whereHas('companyBranch', function ($query) use ($branch) {
                    $query->where('id', $branch);
                });
            })
            ->when($employee_status, function ($query) use ($employee_status) {
                $query->where('status', $employee_status);
            })
            ->with('companyBranch.company', 'designation', 'department')->paginate(25);

        $salaryItems->through(fn($q) => SalaryTransformer::employee($q));

        return response()->json([
            'constants' => [
                'employee_statuses' => $statuses,
                'departments' => $departments,
                'branches' => $branches,
                'salary_types' => $salaryTypes,
            ],
            'employees' => $salaryItems,
        ], 200);
    }

    public function salaryItemStore(StoreSalaryItemRequest $request, Employee $employee)
    {
        if ($employee->company->id != $this->company->id) {
            return response()->json([
                'message' => 'This employee is not associated with your company.',
            ], 403);
        }

        $salaryItem = $employee->salaryItems()->create([
            'salary_type_id' => $request->salary_type_id,
            'status' => $request->status,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'salaryItem' => SalaryTransformer::salaryItem($salaryItem->fresh()),
        ], 201);
    }

    public function salaryItemUpdate(StoreSalaryItemRequest $request, SalaryItem $salaryItem)
    {
        $salaryItem->update([
            'salary_type_id' => $request->salary_type_id,
            'status' => $request->status,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'salaryItem' => SalaryTransformer::salaryItem($salaryItem->fresh()),
        ], 200);
    }

    public function salaryItemDestroy(SalaryItem $salaryItem)
    {
        $salaryItem->delete();

        return response()->json([
            'message' => 'Salary item deleted successfully.',
        ], 200);
    }

    public function salaryShow(Employee $employee)
    {
        // Eager load all salary items with their types in a single query
        $salaryItems = $employee->salaryItems()->with('salaryType')->get();

        // Group items by type
        $itemsByType = $salaryItems->groupBy(function ($item) {
            return $item->salaryType->type;
        });

        // Get the items for each type (or empty collection if none exist)
        $deductionItems = $itemsByType->get('deduction', collect());
        $allowanceItems = $itemsByType->get('allowance', collect());
        $contributionItems = $itemsByType->get('company_contribution', collect());

        // Transform items for API response
        $transformItems = function ($items) {
            return $items->map(fn($item) => SalaryTransformer::salaryItem($item));
        };

        return response()->json([
            'basic_salary' => $employee->basic_salary ?? 0,
            'deductions' => [
                'total' => $employee->salaryItemDeductions(),
                'items' => $transformItems($deductionItems),
            ],
            'allowances' => [
                'total' => $employee->salaryItemAllowances(),
                'items' => $transformItems($allowanceItems),
            ],
            'company_contributions' => [
                'total' => $contributionItems->sum('amount'),
                'items' => $transformItems($contributionItems),
            ],
            'total_salary' => $employee->salaryItemTotal()
        ], 200);
    }
}
