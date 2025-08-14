<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\SalaryTransformer;
use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Models\HRM\CompanyBranch;
use App\Models\HRM\Employee;
use App\Models\Salary\SalaryProcess;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryProcessController extends Controller
{
    protected Company $company;

    public function __construct()
    {
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
        $salaryProcesses = SalaryProcess::query()
            ->with('companyBranch')
            ->whereHas('companyBranch', function ($query) {
                $query->where('company_id', $this->company->id);
            })
            ->paginate(25);

        $salaryProcesses->through(function ($salaryProcess) {
            return SalaryTransformer::salaryProcess($salaryProcess);
        });

        return response()->json([
            'salaryProcesses' => $salaryProcesses,
        ], 200);
    }

    public function store(Request $request, CompanyBranch $companyBranch)
    {
        if ($companyBranch->company_id != $this->company->id) {
            return response()->json([
                'message' => 'This company branch is not associated with your company.',
            ], 403);
        }

        $date = Carbon::parse($request->date);

        $salaryProcess = $companyBranch->salaryProcesses()->create([
            'year' => $date->year,
            'month' => $date->month,
            'status' => $request->status,
        ]);

        $employees = $companyBranch->employees()->with('salaryItems')->get();

        foreach ($employees as $employee) {
            $item = $employee->salaryProcessItems()->create([
                'salary_process_id' => $salaryProcess->id,
                'employee_id' => $employee->id,
                'date' => $date,
                'basic_amount' => $employee->basic_salary,
                'allowance_amount' => $employee->salaryItemAllowances(),
                'deduction_amount' => $employee->salaryItemDeductions(),
                'company_contribution_amount' => $employee->salaryItemContributions(),
                'total_amount' => $employee->salaryItemTotal(),
            ]);
            foreach ($employee->salaryItems as $salaryItem) {
                $item->salaryProcessItemDetails()->create([
                    'salary_type_id' => $salaryItem->salary_type_id,
                    'amount' => $salaryItem->amount,
                ]);
            }
        }

        return response()->json([
            'salaryProcess' => SalaryTransformer::salaryProcess($salaryProcess),
        ], 201);
    }
}
