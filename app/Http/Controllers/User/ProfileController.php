<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\EmployeeTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->employee;

        return response()->json([
            'profile' => EmployeeTransformer::transform($profile),
            'permissions' => $profile->user->getDirectPermissions()->transform(fn($role) => EmployeeTransformer::employeeRoles($role))
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $profile = auth()->user()->employee;

        $profile->update($request->validated());
        auth()->user()->update([
            'email' => $request->validated()['email']
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => EmployeeTransformer::transform($profile),
        ], 200);
    }
}
