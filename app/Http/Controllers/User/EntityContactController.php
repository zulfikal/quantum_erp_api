<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner\Entity;
use App\Models\HRM\Company;
use Illuminate\Http\Request;
use App\Helpers\Transformers\EntityTransformer;
use App\Http\Requests\StoreEntityContactRequest;
use App\Models\BusinessPartner\EntityContact;

class EntityContactController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:entity_contact.index')->only(['index']);
        $this->middleware('can:entity_contact.show')->only(['show']);
        $this->middleware('can:entity_contact.create')->only(['store']);
        $this->middleware('can:entity_contact.edit')->only(['update']);
        $this->middleware('can:entity_contact.destroy')->only(['destroy']);

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
                'message' => 'You are not authorized to view this business partner contact.',
            ], 403);
        }

        $contacts = $entity->contacts->transform(fn($contact) => EntityTransformer::contact($contact));

        return response()->json($contacts);
    }

    public function store(Entity $entity, StoreEntityContactRequest $request)
    {
        if ($entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to add this business partner contact.',
            ], 403);
        }

        $contact = $entity->contacts()->create($request->validated());

        return response()->json([
            'message' => 'Contact created successfully',
            'contact' => EntityTransformer::contact($contact),
        ], 201);
    }

    public function show(EntityContact $contact)
    {
        if ($contact->entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this business partner contact.',
            ], 403);
        }

        return response()->json(EntityTransformer::contact($contact), 200);
    }

    public function update(EntityContact $contact, StoreEntityContactRequest $request)
    {
        if ($contact->entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this business partner contact.',
            ], 403);
        }

        $contact->update($request->validated());

        return response()->json([
            'message' => 'Contact updated successfully',
            'contact' => EntityTransformer::contact($contact->refresh()),
        ], 200);
    }

    public function destroy(EntityContact $contact)
    {
        if ($contact->entity->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this business partner contact.',
            ], 403);
        }

        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully',
        ], 200);
    }
}
