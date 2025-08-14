<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Claim;
use App\Models\HRM\ClaimType;

final class ClaimTransformer
{
    public static function claimType(ClaimType $claimType): array
    {
        return [
            'id' => $claimType->id,
            'name' => $claimType->name,
        ];
    }

    public static function claimList(Claim $claim): array
    {
        return [
            'id' => $claim->id,
            'claim_type' => self::claimType($claim->claimType),
            'employee' => $claim->employee->full_name,
            'branch' => $claim->employee->companyBranch->name,
            'designation' => $claim->employee->designation->name,
            'department' => $claim->employee->department->name,
            'amount' => $claim->amount,
            'approved_amount' => $claim->approved_amount ?? 0,
            'request_date' => $claim->request_date->format('l, d F Y'),
            'description' => $claim->description,
            'responded_by' => $claim->responded_by ? UserTransformer::transform($claim->user) : null,
            'responded_at' => $claim->responded_at ? $claim->responded_at->format('l, d F Y') : null,
            'responded_note' => $claim->responded_note,
            'status' => $claim->status,
        ];
    }
}
