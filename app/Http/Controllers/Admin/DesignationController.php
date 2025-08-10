<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\Company;
use App\Helpers\Transformers\DesignationTransformer;
use App\Http\Requests\StoreDesignationRequest;
use App\Models\HRM\Designation;

class DesignationController extends Controller
{
    public function index(Company $company)
    {
        $designations = $company->designations;

        $designations = $designations->transform(fn($q) => DesignationTransformer::transform($q));

        return response()->json([
            'designations' => $designations,
        ]);
    }

    public function store(StoreDesignationRequest $request, Company $company)
    {
        $designation = $company->designations()->create($request->validated());

        return response()->json([
            'message' => 'Designation stored successfully',
            'designation' => DesignationTransformer::transform($designation),
        ], 201);
    }

    public function update(StoreDesignationRequest $request, Designation $designation)
    {
        $designation->update($request->validated());

        return response()->json([
            'message' => 'Designation updated successfully',
            'designation' => DesignationTransformer::transform($designation),
        ], 200);
    }
}
