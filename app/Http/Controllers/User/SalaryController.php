<?php

namespace App\Http\Controllers\User;

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
        if (auth()->user()->hasRole('employee')) {
            $employee = auth()->user()->employee;
            $salaryItems = $employee->salaryItems()->with('salaryType')->get();
        }

        if (auth()->user()->hasRole('admin')) {
            $employee = Employee::find($request->employee_id);
            $salaryItems = $employee->salaryItems()->with('salaryType')->get();
        }

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'salaryItems' => $salaryItems->transform(fn($salaryItem) => SalaryTransformer::salaryItem($salaryItem)),
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

    public function salaryShow(Employee $employee)
    {
        // Eager load all salary items with their types in a single query
        $salaryItems = $employee->salaryItems()->with('salaryType')->get();
        
        // Group items by type
        $itemsByType = $salaryItems->groupBy(function($item) {
            return $item->salaryType->type;
        });
        
        // Get the items for each type (or empty collection if none exist)
        $deductionItems = $itemsByType->get('deduction', collect());
        $allowanceItems = $itemsByType->get('allowance', collect());
        $contributionItems = $itemsByType->get('company_contribution', collect());
        
        // Transform items for API response
        $transformItems = function($items) {
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
