<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Roles
        $super_admin_role = Role::create(['name' => 'super_admin', 'guard_name' => 'web', 'display_name' => 'Super Admin']);
        $admin_role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'display_name' => 'Admin']);
        $employee_role = Role::create(['name' => 'employee', 'guard_name' => 'web', 'display_name' => 'Employee']);

        //Permissions
        $permissions = [
            ['name' => 'company.index', 'display_name' => 'Company Index', 'guard_name' => 'web'],
            ['name' => 'company.show', 'display_name' => 'Company Show', 'guard_name' => 'web'],
            ['name' => 'company.create', 'display_name' => 'Company Create', 'guard_name' => 'web'],
            ['name' => 'company.edit', 'display_name' => 'Company Edit', 'guard_name' => 'web'],
            ['name' => 'company.destroy', 'display_name' => 'Company Destroy', 'guard_name' => 'web'],
            ['name' => 'company_branch.index', 'display_name' => 'Company Branch Index', 'guard_name' => 'web'],
            ['name' => 'company_branch.show', 'display_name' => 'Company Branch Show', 'guard_name' => 'web'],
            ['name' => 'company_branch.create', 'display_name' => 'Company Branch Create', 'guard_name' => 'web'],
            ['name' => 'company_branch.edit', 'display_name' => 'Company Branch Edit', 'guard_name' => 'web'],
            ['name' => 'company_branch.destroy', 'display_name' => 'Company Branch Destroy', 'guard_name' => 'web'],
            ['name' => 'designation.index', 'display_name' => 'Designation Index', 'guard_name' => 'web'],
            ['name' => 'designation.show', 'display_name' => 'Designation Show', 'guard_name' => 'web'],
            ['name' => 'designation.create', 'display_name' => 'Designation Create', 'guard_name' => 'web'],
            ['name' => 'designation.edit', 'display_name' => 'Designation Edit', 'guard_name' => 'web'],
            ['name' => 'designation.destroy', 'display_name' => 'Designation Destroy', 'guard_name' => 'web'],
            ['name' => 'department.index', 'display_name' => 'Department Index', 'guard_name' => 'web'],
            ['name' => 'department.show', 'display_name' => 'Department Show', 'guard_name' => 'web'],
            ['name' => 'department.create', 'display_name' => 'Department Create', 'guard_name' => 'web'],
            ['name' => 'department.edit', 'display_name' => 'Department Edit', 'guard_name' => 'web'],
            ['name' => 'department.destroy', 'display_name' => 'Department Destroy', 'guard_name' => 'web'],
            ['name' => 'employee.index', 'display_name' => 'Employee Index', 'guard_name' => 'web'],
            ['name' => 'employee.show', 'display_name' => 'Employee Show', 'guard_name' => 'web'],
            ['name' => 'employee.create', 'display_name' => 'Employee Create', 'guard_name' => 'web'],
            ['name' => 'employee.edit', 'display_name' => 'Employee Edit', 'guard_name' => 'web'],
            ['name' => 'employee.destroy', 'display_name' => 'Employee Destroy', 'guard_name' => 'web'],
            ['name' => 'attendance.index', 'display_name' => 'Attendance Index', 'guard_name' => 'web'],
            ['name' => 'attendance.show', 'display_name' => 'Attendance Show', 'guard_name' => 'web'],
            ['name' => 'attendance.create', 'display_name' => 'Attendance Create', 'guard_name' => 'web'],
            ['name' => 'attendance.edit', 'display_name' => 'Attendance Edit', 'guard_name' => 'web'],
            ['name' => 'attendance.destroy', 'display_name' => 'Attendance Destroy', 'guard_name' => 'web'],
            ['name' => 'leave.index', 'display_name' => 'Leave Index', 'guard_name' => 'web'],
            ['name' => 'leave.show', 'display_name' => 'Leave Show', 'guard_name' => 'web'],
            ['name' => 'leave.create', 'display_name' => 'Leave Create', 'guard_name' => 'web'],
            ['name' => 'leave.edit', 'display_name' => 'Leave Edit', 'guard_name' => 'web'],
            ['name' => 'leave.destroy', 'display_name' => 'Leave Destroy', 'guard_name' => 'web'],
            ['name' => 'salary.index', 'display_name' => 'Salary Index', 'guard_name' => 'web'],
            ['name' => 'salary.show', 'display_name' => 'Salary Show', 'guard_name' => 'web'],
            ['name' => 'salary.create', 'display_name' => 'Salary Create', 'guard_name' => 'web'],
            ['name' => 'salary.edit', 'display_name' => 'Salary Edit', 'guard_name' => 'web'],
            ['name' => 'salary.destroy', 'display_name' => 'Salary Destroy', 'guard_name' => 'web'],
            ['name' => 'bank.index', 'display_name' => 'Bank Index', 'guard_name' => 'web'],
            ['name' => 'bank.show', 'display_name' => 'Bank Show', 'guard_name' => 'web'],
            ['name' => 'bank.create', 'display_name' => 'Bank Create', 'guard_name' => 'web'],
            ['name' => 'bank.edit', 'display_name' => 'Bank Edit', 'guard_name' => 'web'],
            ['name' => 'bank.destroy', 'display_name' => 'Bank Destroy', 'guard_name' => 'web'],
            ['name' => 'leave_type.index', 'display_name' => 'Leave Type Index', 'guard_name' => 'web'],
            ['name' => 'leave_type.show', 'display_name' => 'Leave Type Show', 'guard_name' => 'web'],
            ['name' => 'leave_type.create', 'display_name' => 'Leave Type Create', 'guard_name' => 'web'],
            ['name' => 'leave_type.edit', 'display_name' => 'Leave Type Edit', 'guard_name' => 'web'],
            ['name' => 'leave_type.destroy', 'display_name' => 'Leave Type Destroy', 'guard_name' => 'web'],
            ['name' => 'salary_type.index', 'display_name' => 'Salary Type Index', 'guard_name' => 'web'],
            ['name' => 'salary_type.show', 'display_name' => 'Salary Type Show', 'guard_name' => 'web'],
            ['name' => 'salary_type.create', 'display_name' => 'Salary Type Create', 'guard_name' => 'web'],
            ['name' => 'salary_type.edit', 'display_name' => 'Salary Type Edit', 'guard_name' => 'web'],
            ['name' => 'salary_type.destroy', 'display_name' => 'Salary Type Destroy', 'guard_name' => 'web'],
            ['name' => 'salary_item.index', 'display_name' => 'Salary Item Index', 'guard_name' => 'web'],
            ['name' => 'salary_item.show', 'display_name' => 'Salary Item Show', 'guard_name' => 'web'],
            ['name' => 'salary_item.create', 'display_name' => 'Salary Item Create', 'guard_name' => 'web'],
            ['name' => 'salary_item.edit', 'display_name' => 'Salary Item Edit', 'guard_name' => 'web'],
            ['name' => 'salary_item.destroy', 'display_name' => 'Salary Item Destroy', 'guard_name' => 'web'],
            ['name' => 'salary_process.index', 'display_name' => 'Salary Process Index', 'guard_name' => 'web'],
            ['name' => 'salary_process.show', 'display_name' => 'Salary Process Show', 'guard_name' => 'web'],
            ['name' => 'salary_process.create', 'display_name' => 'Salary Process Create', 'guard_name' => 'web'],
            ['name' => 'salary_process.edit', 'display_name' => 'Salary Process Edit', 'guard_name' => 'web'],
            ['name' => 'salary_process.destroy', 'display_name' => 'Salary Process Destroy', 'guard_name' => 'web'],
        ];

        Permission::insert($permissions);

        // Grant all permissions to super_admin
        $all_permissions = Permission::all();
        $super_admin_role->syncPermissions($all_permissions);
    }
}
