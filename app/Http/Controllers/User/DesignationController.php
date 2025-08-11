<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Helpers\Transformers\CompanyTransformer;
use App\Http\Requests\StoreDesignationRequest;
use App\Models\HRM\Designation;

class DesignationController extends Controller
{
    private Company $company;

    public function __construct()
    {
        $this->middleware('can:designation.index')->only('index');
        $this->middleware('can:designation.create')->only('store');
        $this->middleware('can:designation.show')->only('show');
        $this->middleware('can:designation.edit')->only('update');

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
        $designations = $this->company->designations;

        $designations = $designations->transform(fn($q) => CompanyTransformer::designation($q));

        return response()->json([
            'designations' => $designations,
        ], 200);
    }

    public function store(StoreDesignationRequest $request)
    {
        $designation = $this->company->designations()->create([
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
        if ($designation->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

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

    public function show(Designation $designation)
    {
        if ($designation->company_id !== $this->company->id) {
            return response()->json([
                'message' => 'This operation is not authorized',
            ], 403);
        }

        return response()->json([
            'designation' => CompanyTransformer::designation($designation),
        ], 200);
    }
}
