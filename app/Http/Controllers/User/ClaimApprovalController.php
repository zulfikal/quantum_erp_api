<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\Company;
use App\Helpers\Transformers\ClaimTransformer;
use App\Models\HRM\Claim;
use App\Models\HRM\ClaimType;

class ClaimApprovalController extends Controller
{
    protected Company $company;

    /**
     * Get claim status counts using a single optimized query
     * 
     * @return array
     */
    protected function getClaimStatusCounts(): array
    {
        // Get all status counts in a single query
        $statusCounts = $this->company->claims()
            ->selectRaw('claims.status as status_key, COUNT(*) as count')
            ->groupBy('claims.status')
            ->pluck('count', 'status_key')
            ->toArray();

        // Extract counts with defaults for missing statuses
        return [
            'pendingClaimsCount' => $statusCounts['pending'] ?? 0,
            'approvedClaimsCount' => $statusCounts['approved'] ?? 0,
            'rejectedClaimsCount' => $statusCounts['rejected'] ?? 0,
        ];
    }

    public function __construct()
    {
        $this->middleware('can:claim_approval.index')->only(['index']);
        $this->middleware('can:claim_approval.show')->only(['show']);
        $this->middleware('can:claim_approval.approval')->only(['approval']);

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
        $claims = $this->company->claims()
            ->with('claimType', 'employee.companyBranch', 'employee.designation', 'employee.department')
            ->latest()
            ->paginate(25);

        $claims->through(fn($claim) => ClaimTransformer::claimList($claim));

        return response()->json([
            'constants' => [
                'claim_types' => ClaimType::all()->transform(fn($claim_type) => ClaimTransformer::claimType($claim_type)),
            ],
            'statistics' => [
                'allClaimsCount' => $this->company->claims()->count(),
                ...$this->getClaimStatusCounts(),
            ],
            'claims' => $claims,
        ], 200);
    }

    public function show(Claim $claim)
    {
        if ($claim->employee->companyBranch->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this claim',
            ], 401);
        }
        return response()->json(ClaimTransformer::claimList($claim), 200);
    }

    public function approval(Claim $claim, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled,paid',
            'approved_amount' => 'required_if:status,approved|numeric',
            'responded_note' => 'nullable|string',
        ]);

        if ($claim->employee->companyBranch->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to respond to this claim',
            ], 401);
        }

        if ($claim->status == 'approved' && $request->status == 'approved') {
            return response()->json([
                'message' => 'Claim is already approved',
            ], 400);
        }

        $claim->update([
            'status' => $request->status,
            'approved_amount' => $request->approved_amount,
            'responded_by' => auth()->user()->id,
            'responded_at' => now(),
            'responded_note' => $request->responded_note,
        ]);

        return response()->json([
            'message' => 'Claim ' . $request->status . ' successfully',
            'data' => ClaimTransformer::claimList($claim),
        ], 200);
    }
}
