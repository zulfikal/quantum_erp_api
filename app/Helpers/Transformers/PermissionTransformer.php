<?php

namespace App\Helpers\Transformers;

use Spatie\Permission\Models\Permission;

class PermissionTransformer
{
    public static function permission(Permission $permission, bool $isAssigned)
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'display_name' => $permission->display_name,
            'description' => $permission->description,
            'is_assigned' => $isAssigned,
        ];
    }
}
