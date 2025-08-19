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
        $search = $request->input('search');

        $entity_types = EntityStaticData::types();
        $entity_status = EntityStaticData::status();
        $entity_address_types = EntityStaticData::addressTypes();
        $entity_contact_types = EntityStaticData::contactTypes();

        $entities = Entity::where('company_id', $this->company->id)
            ->when($search, fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->with('contacts', 'addresses', 'createdBy', 'company')
            ->paginate(25);

        $entities->through(fn($entity) => EntityTransformer::entities($entity));

        return response()->json([
            'constants' => [
                'types' => $entity_types,
                'status' => $entity_status,
                'address_types' => $entity_address_types,
                'contact_types' => $entity_contact_types,
            ],
            'statistics' => array_map('intval', (array) DB::table('entities')
                ->where('company_id', $this->company->id)
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

            $entity->addresses()->createMany($request['address']);
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
}
