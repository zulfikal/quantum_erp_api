<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\ClaimTransformer;
use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\HRM\Claim;
use App\Models\HRM\ClaimType;

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
        // Determine the base query builder depending on user role
        $baseQuery = auth()->user()->hasRole('admin')
            ? $this->company->claims()
            : auth()->user()->employee->claims();

        // Get paginated claims with relationships
        $claims = (clone $baseQuery)
            ->with([
                'claimType',
                'employee.companyBranch',
                'employee.designation',
                'employee.department',
                'user'
            ])
            ->latest()
            ->paginate(25);

        // Transform claims
        $claims->through(fn($claim) => ClaimTransformer::claimList($claim));

        // Get statistics counts in separate queries but more efficiently
        $allClaimsCount = (clone $baseQuery)->count();
        
        // Get all status counts in a single query
        $statusCounts = (clone $baseQuery)
            ->selectRaw('claims.status as status_key, COUNT(*) as count')
            ->groupBy('claims.status')
            ->pluck('count', 'status_key')
            ->toArray();
            
        // Extract counts with defaults for missing statuses
        $pendingClaimsCount = $statusCounts['pending'] ?? 0;
        $approvedClaimsCount = $statusCounts['approved'] ?? 0;
        $rejectedClaimsCount = $statusCounts['rejected'] ?? 0;

        $claim_types = ClaimType::all();
        $claim_types->transform(fn($claim_type) => ClaimTransformer::claimType($claim_type));

        return response()->json([
            'constants' => [
                'claim_types' => $claim_types,
            ],
            'statistics' => [
                'allClaimsCount' => $allClaimsCount,
                'pendingClaimsCount' => $pendingClaimsCount,
                'approvedClaimsCount' => $approvedClaimsCount,
                'rejectedClaimsCount' => $rejectedClaimsCount,
            ],
            'claims' => $claims,
        ], 200);
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

        if (auth()->user()->hasRole('employee')) {
            if ($request->employee_id && $request->employee_id !== auth()->user()->employee->id) {
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
