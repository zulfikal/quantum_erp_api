<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Helpers\Transformers\CompanyTransformer;
use App\Http\Requests\StoreDesignationRequest;
use App\Models\HRM\Designation;

class DesignationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin');
    }

    public function index(Company $company)
    {
        $designations = $company->designations;

        $designations = $designations->transform(fn($q) => CompanyTransformer::designation($q));

        return response()->json([
            'designations' => $designations,
        ]);
    }

    public function store(StoreDesignationRequest $request, Company $company)
    {
        $designation = $company->designations()->create([
            'name' => $request->name,
            'code' => str_replace(" ", "_", strtolower($request->name)),
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Designation stored successfully',
            'designation' => CompanyTransformer::designation($designation),
        ], 201);
    }

    public function update(StoreDesignationRequest $request, Designation $designation)
    {
        $designation->update([
            'name' => $request->name,
            'code' => str_replace(" ", "_", strtolower($request->name)),
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Designation updated successfully',
            'designation' => CompanyTransformer::designation($designation),
        ], 200);
    }
}
