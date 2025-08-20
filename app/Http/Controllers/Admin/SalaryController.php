<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaryItemRequest;
use Illuminate\Http\Request;
use App\Models\Salary\SalaryProcess;
use App\Models\Salary\SalaryType;
use App\Http\Requests\StoreSalaryTypeRequest;
use App\Models\HRM\CompanyBranch;
use App\Models\HRM\Employee;
use App\Models\Salary\SalaryItem;
use App\Helpers\Transformers\SalaryTransformer;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin');
    }

    public function salaryItemIndex(Employee $employee)
    {
        $salaryItems = $employee->salaryItems()->with('salaryType')->get();

        $salaryItems->transform(fn($salaryItem) => SalaryTransformer::salaryItem($salaryItem));

        return response()->json([
            'salaryItems' => $salaryItems,
        ], 200);
    }

    public function salaryItemStore(StoreSalaryItemRequest $request, Employee $employee)
    {
        $salaryItem = $employee->salaryItems()->create($request->validated());

        return response()->json([
            'message' => 'Salary item stored successfully',
            'salaryItem' => $salaryItem,
        ], 201);
    }

    public function salaryItemUpdate(StoreSalaryItemRequest $request, SalaryItem $salaryItem)
    {
        $salaryItem->update($request->validated());

        return response()->json([
            'message' => 'Salary item updated successfully',
            'salaryItem' => SalaryTransformer::salaryItem($salaryItem),
        ], 200);
    }

    public function salaryProcessIndex(CompanyBranch $companyBranch)
    {
        $salaryProcesses = $companyBranch->salaryProcesses;

        return response()->json([
            'salaryProcesses' => $salaryProcesses,
        ], 200);
    }

    public function salaryProcessStore(StoreSalaryProcessRequest $request, CompanyBranch $companyBranch)
    {
        $salaryProcess = $companyBranch->salaryProcesses()->create($request->validated());

        return response()->json([
            'message' => 'Salary process stored successfully',
            'salaryProcess' => $salaryProcess,
        ], 201);
    }
}
