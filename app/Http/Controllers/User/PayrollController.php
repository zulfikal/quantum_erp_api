<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Helpers\Transformers\PayrollTransformer;
use App\Models\Salary\SalaryProcessItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $salaryProcessItems = auth()->user()->employee
            ->salaryProcessItems()
            ->whereHas('salaryProcess', function ($query) {
                $query->where('status', 'paid');
            })
            ->with('salaryProcess', 'employee.companyBranch.company')
            ->paginate(25);

        $salaryProcessItems->through(fn($salaryProcessItem) => PayrollTransformer::payrollList($salaryProcessItem));

        return response()->json([
            'data' => $salaryProcessItems,
        ], 200);
    }

    public function show(SalaryProcessItem $salaryProcessItem)
    {
        return response()->json([
            'data' => PayrollTransformer::payrollDetail($salaryProcessItem),
        ], 200);
    }

    public function pdf(SalaryProcessItem $salaryProcessItem)
    {
        $pdf = PDF::loadView('pdf.payroll', [
            'data' => PayrollTransformer::payrollDetail($salaryProcessItem),
        ]);

        return $pdf->stream();
    }
}
