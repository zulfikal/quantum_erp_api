<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required',
            'token' => 'nullable',
        ]);

        $user = User::query()
            ->where('email', $request->email)
            // ->orWhere('username', $request->email)
            // ->orWhereHas('employee', fn($q) => $q->where('staff_id', $request->email))
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // $user->tokens()->delete();

        $token = $user->createToken($request->device_name);

        // if ($request->token) {
        //     MobileUser::updateOrCreate(
        //         ['user_id' => $user->id],
        //         ['notification_token' => $request->token]
        //     );
        // }

        return response()->json([
            'plainTextToken' => $token->plainTextToken,
            'info' => [
                'user_id' => (string)$user->id,
                // 'employee_id' => $user->employee->id,
                'user_name' => strtoupper($user->name),
                'device' => $request->device_name
            ],
            // 'roles' => $user->roles->transform(fn($q) => ['name' => $q->name, 'full_name' => $q->full_name])
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}
