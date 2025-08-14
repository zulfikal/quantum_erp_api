<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\ClaimTransformer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\ClaimType;

class ClaimTypeController extends Controller
{
    public function index()
    {
        $claimTypes = ClaimType::all();
        $claimTypes->transform(fn($claimType) => ClaimTransformer::claimType($claimType));
        return response()->json([
            'data' => $claimTypes,
        ], 200);
    }
}
