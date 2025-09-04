<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\EmployeeTransformer;
use App\Helpers\Transformers\PermissionTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->employee;
        $permissions = auth()->user()->getAllPermissions()->transform(fn($q) => PermissionTransformer::permission($q, true));

        return response()->json([
            'profile' => EmployeeTransformer::transform($profile),
            'permissions' => $permissions
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
                ->toMediaCollection('avatar', 'public');

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

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 400);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ], 200);
    }
}
