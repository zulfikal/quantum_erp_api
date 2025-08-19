<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner\Entity;
use App\Models\HRM\Company;
use Illuminate\Http\Request;
use App\Helpers\Transformers\EntityTransformer;
use App\Http\Requests\StoreEntityAddressRequest;
use App\Models\BusinessPartner\EntityAddress;

class EntityAddressController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:entity.index')->only(['index']);
        $this->middleware('can:entity.show')->only(['show']);
        $this->middleware('can:entity.create')->only(['store']);
        $this->middleware('can:entity.edit')->only(['update']);
        $this->middleware('can:entity.destroy')->only(['destroy']);

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index(Entity $entity)
    {
        if ($entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this business partner address.',
            ], 403);
        }

        $addresses = $entity->addresses->transform(fn($address) => EntityTransformer::address($address));

        return response()->json($addresses, 200);
    }

    public function show(EntityAddress $address)
    {
        if ($address->entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this business partner address.',
            ], 403);
        }

        return response()->json(EntityTransformer::address($address), 200);
    }

    public function store(Entity $entity, StoreEntityAddressRequest $request)
    {
        if ($entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this business partner address.',
            ], 403);
        }

        $address = $entity->addresses()->create($request->validated());

        return response()->json([
            'message' => 'Address created successfully',
            'address' => EntityTransformer::address($address),
        ], 201);
    }

    public function update(EntityAddress $address, StoreEntityAddressRequest $request)
    {
        if ($address->entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this business partner address.',
            ], 403);
        }

        $address->update($request->validated());

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => EntityTransformer::address($address->refresh()),
        ], 200);
    }

    public function destroy(EntityAddress $address)
    {
        if ($address->entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this business partner address.',
            ], 403);
        }

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully.',
        ], 200);
    }
}
