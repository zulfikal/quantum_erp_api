<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\EmployeeTransformer;
use App\Helpers\Transformers\PermissionTransformer;
use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Models\HRM\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:permission.index')->only('index');
        $this->middleware('can:permission.manage')->only('manage');

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index(Employee $employee)
    {
        $permissions = Permission::whereNotIn('id', [1, 3, 4, 5])->get();

        $employee->load('user.permissions');

        if (!$employee->user) {
            return response()->json([
                'message' => 'Employee does not have an associated user account',
                'permissions' => $permissions->transform(fn($permission) => PermissionTransformer::permission($permission, false)),
            ], 200);
        }

        return response()->json([
            'employee' => EmployeeTransformer::transform($employee),
            'permissions' => $permissions->transform(function ($permission) use ($employee) {
                try {
                    $isAssigned = $employee->user->hasPermissionTo($permission);
                } catch (\Exception $e) {
                    $isAssigned = false;
                }
                return PermissionTransformer::permission($permission, $isAssigned);
            }),
        ], 200);
    }

    public function manage(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_ids' => 'nullable|exists:permissions,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->employee->company->id != $this->company->id) {
            return response()->json([
                'message' => 'The user is not associated with the company.',
            ], 400);
        }

        $permissions = Permission::whereIn('id', $validated['permission_ids'])->get();

        $user->syncPermissions($permissions);

        return response()->json([
            'message' => 'The permission has been updated successfully.',
        ], 200);
    }
}
