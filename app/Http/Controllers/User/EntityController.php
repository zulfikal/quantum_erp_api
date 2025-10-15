<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\EntityStaticData;
use App\Helpers\Transformers\EntityTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\BusinessPartner\Entity;
use App\Models\HRM\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntityController extends Controller
{
    protected Company $company;

    private $response;

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

    public function index(Request $request)
    {
        $name = $request->input('name');
        $type = $request->input('type');
        $status = $request->input('status');

        $entity_types = EntityStaticData::types();
        $entity_status = EntityStaticData::status();
        $entity_address_types = EntityStaticData::addressTypes();
        $entity_contact_types = EntityStaticData::contactTypes();
        $entity_identity_types = EntityStaticData::identityTypes();

        $entities = Entity::where('company_id', $this->company->id)
            ->when($name, fn($query) => $query->where('name', 'like', "%{$name}%"))
            ->when($type, fn($query) => $query->where('type', $type))
            ->when($status, fn($query) => $query->where('status', $status))
            ->with('contacts', 'addresses', 'createdBy', 'company', 'identityType')
            ->latest()
            ->paginate(25);

        $entities->through(fn($entity) => EntityTransformer::entities($entity));

        return response()->json([
            'constants' => [
                'types' => $entity_types,
                'status' => $entity_status,
                'address_types' => $entity_address_types,
                'contact_types' => $entity_contact_types,
                'identity_types' => $entity_identity_types,
            ],
            'statistics' => array_map('intval', (array) DB::table('entities')
                ->where('company_id', $this->company->id)
                ->when($type, fn($query) => $query->where('type', $type))
                ->selectRaw('
                    COALESCE(COUNT(*), 0) as total,
                    COALESCE(SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END), 0) as active,
                    COALESCE(SUM(CASE WHEN status = "inactive" THEN 1 ELSE 0 END), 0) as inactive
                ')
                ->first()),
            'entities' => $entities,
        ], 200);
    }

    public function store(StoreEntityRequest $request)
    {
        DB::transaction(function () use ($request) {
            $entity = auth()->user()
                ->employee
                ->entities()
                ->create(
                    array_merge(
                        $request['entity'],
                        ['company_id' => $this->company->id]
                    )
                );

            // Process addresses with default flag handling
            $addresses = $request['address'];
            $hasDefaultAddress = false;
            $firstAddressId = null;

            // Create addresses and track if any is set as default
            foreach ($addresses as $index => $addressData) {
                $address = $entity->addresses()->create($addressData);

                // Store the first address ID in case we need to set it as default
                if ($index === 0) {
                    $firstAddressId = $address->id;
                }

                // Check if this address is marked as default
                if (isset($addressData['is_default']) && $addressData['is_default']) {
                    // If we already found a default address, set this one to false
                    if ($hasDefaultAddress) {
                        $address->update(['is_default' => false]);
                    } else {
                        $hasDefaultAddress = true;
                        // Ensure all other addresses are not default
                        $entity->addresses()
                            ->where('id', '!=', $address->id)
                            ->update(['is_default' => false]);
                    }
                }
            }

            // If no address was set as default, set the first one as default
            if (!$hasDefaultAddress && $firstAddressId) {
                $entity->addresses()->where('id', $firstAddressId)->update(['is_default' => true]);
            }

            $entity->contacts()->createMany($request['contacts']);

            $this->response = $entity;
        });

        return response()->json([
            'message' => 'Business partner created successfully',
            'data' => EntityTransformer::entity($this->response)
        ], 201);
    }

    public function show(Entity $entity)
    {
        if ($entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this business partner.',
            ], 403);
        }
        return response()->json(EntityTransformer::entity($entity), 200);
    }

    public function update(UpdateEntityRequest $request, Entity $entity)
    {
        if ($entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this business partner.',
            ], 403);
        }
        $entity->update($request->validated());
        return response()->json([
            'message' => 'Business partner updated successfully',
            'data' => EntityTransformer::entity($entity->refresh())
        ], 200);
    }

    public function destroy(Entity $entity)
    {
        if ($entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this business partner.',
            ], 403);
        }
        $entity->delete();
        return response()->json([
            'message' => 'Business partner deleted successfully',
        ]);
    }

    public function globalEntity()
    {
        $entities = Entity::where('company_id', $this->company->id)
            ->with('contacts', 'addresses', 'createdBy', 'company')
            ->latest()
            ->get();

        $entities->transform(fn($entity) => EntityTransformer::entities($entity));

        return response()->json([
            'entities' => $entities,
        ], 200);
    }
}
