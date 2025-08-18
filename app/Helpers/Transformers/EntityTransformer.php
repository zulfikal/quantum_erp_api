<?php

namespace App\Helpers\Transformers;

use App\Models\BusinessPartner\Entity;
use App\Models\BusinessPartner\EntityAddress;
use App\Models\BusinessPartner\EntityContact;

class EntityTransformer
{
    public static function address(EntityAddress $address)
    {
        return [
            'id' => $address->id,
            'address_1' => $address->address_1,
            'address_2' => $address->address_2,
            'city' => $address->city,
            'state' => $address->state,
            'zip_code' => $address->zip_code,
            'country' => $address->country,
            'notes' => $address->notes,
            'type' => $address->type,
            'label' => $address->type_label,
        ];
    }

    public static function contact(EntityContact $contact)
    {
        return [
            'id' => $contact->id,
            'type' => $contact->type,
            'label' => $contact->type_label,
            'value' => $contact->value,
            'notes' => $contact->notes,
        ];
    }

    public static function entity(Entity $entity)
    {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
            'type' => $entity->type,
            'label' => $entity->type_label,
            'status' => $entity->status,
            'entity_id' => $entity->entity_id,
            'tin_number' => $entity->tin_number,
            'website' => $entity->website,
            'notes' => $entity->notes,
            'created_by' => $entity->createdBy->full_name,
            'addresses' => $entity->addresses->transform(function (EntityAddress $address) {
                return self::address($address);
            }),
            'contacts' => $entity->contacts->transform(function (EntityContact $contact) {
                return self::contact($contact);
            }),
        ];
    }

    public static function entities(Entity $entity)
    {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
            'type' => $entity->type,
            'label' => $entity->type_label,
            'status' => $entity->status,
            'entity_id' => $entity->entity_id,
            'tin_number' => $entity->tin_number,
            'website' => $entity->website,
            'notes' => $entity->notes,
            'created_by' => $entity->createdBy->full_name,
        ];
    }
}
