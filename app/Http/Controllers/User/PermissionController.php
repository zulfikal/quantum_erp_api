<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\PermissionTransformer;
use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('role:admin');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $permissions = Permission::whereNotIn('id', [1, 3, 4, 5])->get();
        return response()->json([
            'permissions' => $permissions->transform(fn($permission) => PermissionTransformer::permission($permission)),
        ], 200);
    }

    public function manage(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_ids' => 'required|exists:permissions,id',
            'action' => 'required|in:granted,revoked',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->employee->company->id != $this->company->id) {
            return response()->json([
                'message' => 'The user is not associated with the company.',
            ], 400);
        }

        $permissions = Permission::whereIn('id', $validated['permission_ids'])->get();

        if ($validated['action'] === 'granted') {
            $exists = $user->permissions()->whereIn('permission_id', $permissions->pluck('id'))->get();
            if ($exists->isNotEmpty()) {
                return response()->json([
                    'message' => 'The permission has already been granted to the user.',
                    'exists' => $exists->transform(fn($q) => PermissionTransformer::permission($q)),
                ], 400);
            }
            $user->permissions()->attach($permissions);
        } else {
            $user->permissions()->detach($permissions);
        }

        return response()->json([
            'message' => 'The permission has been ' . $validated['action'] . ' to the user successfully.',
        ], 200);
    }
}
