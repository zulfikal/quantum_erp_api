<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\ClaimTransformer;
use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\HRM\Claim;

class ClaimController extends Controller
{
    protected Company $company;

    public function __construct()
    {

        $this->middleware('can:claim.index')->only(['index']);
        $this->middleware('can:claim.show')->only(['show']);
        $this->middleware('can:claim.create')->only(['store']);
        $this->middleware('can:claim.edit')->only(['update']);
        $this->middleware('can:claim.destroy')->only(['destroy']);

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
        if (auth()->user()->hasRole('admin')) {
            $claims = $this->company->claims()
                ->with('claimType', 'employee.companyBranch', 'employee.designation', 'employee.department', 'user')
                ->latest()
                ->paginate(25);
        }

        if (auth()->user()->hasRole('employee')) {
            $claims = auth()->user()->employee->claims()
                ->with('claimType', 'employee.companyBranch', 'employee.designation', 'employee.department', 'user')
                ->latest()
                ->paginate(25);
        }

        $claims->through(fn($claim) => ClaimTransformer::claimList($claim));

        return response()->json($claims, 200);
    }

    public function show(Claim $claim)
    {
        if (auth()->user()->hasRole('admin')) {
            if ($claim->employee->companyBranch->company_id !== $this->company->id) {
                return response()->json([
                    'message' => 'You are not authorized to view this claim',
                ], 401);
            }
            return response()->json(ClaimTransformer::claimList($claim), 200);
        }

        if (auth()->user()->hasRole('employee')) {
            if (auth()->user()->employee->id !== $claim->employee_id) {
                return response()->json([
                    'message' => 'You are not authorized to view this claim',
                ], 401);
            }
            return response()->json(ClaimTransformer::claimList($claim), 200);
        }
    }

    public function store(StoreClaimRequest $request)
    {
        if (auth()->user()->hasRole('admin')) {
            if ($request->employee_id) {
                if ($request->employee_id->companyBranch->company_id !== $this->company->id) {
                    return response()->json([
                        'message' => 'You are not authorized to create this claim',
                    ], 401);
                }
            }
        }

        if(auth()->user()->hasRole('employee')) {
            if($request->employee_id && $request->employee_id !== auth()->user()->employee->id) {
                return response()->json([
                    'message' => 'You are not authorized to create this claim',
                ], 401);
            }
        }

        $claim = Claim::create([
            'claim_type_id' => $request->claim_type_id,
            'employee_id' => $request->employee_id ?? auth()->user()->employee->id,
            'amount' => $request->amount,
            'request_date' => now(),
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Claim created successfully',
            'data' => ClaimTransformer::claimList($claim),
        ], 201);
    }

    public function update(UpdateClaimRequest $request, Claim $claim)
    {
        if (auth()->user()->hasRole('employee') && auth()->user()->employee->id !== $claim->employee_id) {
            return response()->json([
                'message' => 'You are not authorized to update this claim',
            ], 401);
        }

        if ($this->company->id !== $claim->employee->companyBranch->company_id) {
            return response()->json([
                'message' => 'You are not authorized to update this claim',
            ], 401);
        }
        

        $claim->update($request->validated());

        return response()->json([
            'message' => 'Claim updated successfully',
            'data' => ClaimTransformer::claimList($claim),
        ], 200);
    }

    public function destroy(Claim $claim)
    {
        if (auth()->user()->hasRole('employee') && auth()->user()->employee->id !== $claim->employee_id) {
            return response()->json([
                'message' => 'You are not authorized to delete this claim',
            ], 401);
        }

        if ($this->company->id !== $claim->employee->companyBranch->company_id) {
            return response()->json([
                'message' => 'You are not authorized to delete this claim',
            ], 401);
        }
        $claim->delete();
        return response()->json([
            'message' => 'Claim deleted successfully',
        ], 200);
    }
}
