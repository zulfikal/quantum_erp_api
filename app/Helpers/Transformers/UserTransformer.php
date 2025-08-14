<?php

namespace App\Helpers\Transformers;

use App\Models\User;

class UserTransformer
{
    public static function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }
}
