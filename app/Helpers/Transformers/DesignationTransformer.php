<?php

namespace App\Helpers\Transformers;

use App\Models\HRM\Designation;

class DesignationTransformer
{
    public static function transform(Designation $designation)
    {
        return [
            'id' => $designation->id,
            'name' => $designation->name,
            'status' => $designation->status,
            'code' => $designation->code,
        ];
    }
}