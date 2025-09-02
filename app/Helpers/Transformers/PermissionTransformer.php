<?php

namespace App\Helpers\Transformers;

use Spatie\Permission\Models\Permission;

class PermissionTransformer
{
    public static function permission(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'display_name' => $permission->display_name,
            'description' => $permission->description,
        ];
    }
}
