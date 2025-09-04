<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\EmployeeTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profile = auth()->user()->employee;

        try {
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $filename = hash('sha256', $request->file('avatar')->getClientOriginalName() . time()) . '.' . $extension;

            // Clear existing avatar
            $profile->clearMediaCollection('avatar');

            // Add new avatar
            $profile
                ->addMediaFromRequest('avatar')
                ->usingFileName($filename)
                ->toMediaCollection('avatar');

            return response()->json([
                'message' => 'Avatar updated successfully',
                'profile' => EmployeeTransformer::transform($profile),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Avatar upload failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to upload avatar: ' . $e->getMessage(),
            ], 500);
        }
    }
}
