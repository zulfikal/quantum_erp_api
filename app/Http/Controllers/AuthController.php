<?php

namespace App\Http\Controllers;

use App\Helpers\Transformers\EmployeeTransformer;
use App\Helpers\Transformers\PermissionTransformer;
use App\Models\HRM\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $user->load('roles');

        $permissions = $user->getAllPermissions()->transform(fn($q) => PermissionTransformer::permission($q, true));

        return response()->json([
            'plainTextToken' => $token->plainTextToken,
            'info' => [
                'user_id' => $user->id,
                'employee' => EmployeeTransformer::transform($user->employee),
                'user_name' => strtoupper($user->name),
                'device' => $request->device_name
            ],
            'roles' => $user->roles->transform(fn($q) => ['name' => $q->name, 'display_name' => $q->display_name]),
            'permissions' => $permissions
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function check(Request $request)
    {
        $request->validate([
            'staff_id' => 'required',
            'nric_number' => 'required',
        ], [
            'staff_id.required' => 'Staff ID is required',
            'nric_number.required' => 'NRIC Number is required',
        ]);

        $employee = Employee::where('staff_id', $request->staff_id)->where('nric_number', $request->nric_number)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Employee found',
            'employee' => EmployeeTransformer::transform($employee),
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'staff_id' => 'required',
            'nric_number' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]);

        $employee = Employee::where('staff_id', $request->staff_id)->where('nric_number', $request->nric_number)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        DB::transaction(function () use ($request, $employee) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('employee');

            $user->employee()->save($employee);
        });

        return response()->json([
            'message' => 'User registered successfully',
        ], 201);
    }
}
