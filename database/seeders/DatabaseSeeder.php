<?php

namespace Database\Seeders;

use App\Models\HRM\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(StaticDataSeeder::class);

        //Create companies
        $company = Company::create([
            "name" => "Quantum ERP SDN. BHD.",
            "register_number" => "A109333",
            "tin_number" => "109020020",
            "status" => "active"
        ]);

        //Create admin department
        $adminDepartment = $company->departments()->create([
            "name" => "Admin",
        ]);

        //Create designations
        $name = "System Admin";
        $code = str_replace(" ", "_", strtolower($name));
        $designation = $company->designations()->create([
            "name" => $name,
            "code" => $code
        ]);

        //Create company branches
        $companyBranch = $company->branches()->create([
            "name" => "Main Branch",
            "address_1" => "Address",
            "company_id" => $company->id,
            "city" => "Kuala Lumpur",
            "state" => "Selangor",
            "zip_code" => "00000",
            "country" => "Malaysia",
            "phone" => "+60123456789"
        ]);

        //Create admin employees
        $companyAdmin = $companyBranch->employees()->create([
            "user_id" => null,
            "designation_id" => $designation->id,
            "department_id" => $adminDepartment->id,
            "nric_number" => "000000000000",
            "first_name" => "System",
            "last_name" => "Admin",
            "email" => "admin@example.com",
            "phone" => "000000000000",
            "basic_salary" => 0,
            "gender" => "male",
            "marital_status" => "single",
            "nationality" => "Malaysia",
            "religion" => "Other",
            "address_1" => "Address",
            "city" => "Kuala Lumpur",
            "state" => "Selangor",
            "zip_code" => "00000",
            "country" => "Malaysia",
            "register_number" => "0000000000",
        ]);

        //Create employees
        $companyEmployee = $companyBranch->employees()->create([
            "user_id" => null,
            "designation_id" => $designation->id,
            "department_id" => $adminDepartment->id,
            "nric_number" => "000000000000",
            "first_name" => "Employee",
            "last_name" => "One",
            "email" => "employee@example.com",
            "phone" => "000000000000",
            "basic_salary" => 1500,
            "gender" => "male",
            "marital_status" => "single",
            "nationality" => "Malaysia",
            "religion" => "Other",
            "address_1" => "Address",
            "city" => "Kuala Lumpur",
            "state" => "Selangor",
            "zip_code" => "00000",
            "country" => "Malaysia",
            "register_number" => "0000000000",
        ]);

        //Create employee bank account
        $companyAdmin->bankAccount()->create([
            "bank_id" => 21,
            "account_number" => "000000000000",
            "holder_name" => $companyAdmin->first_name . " " . $companyAdmin->last_name
        ]);

        $companyEmployee->bankAccount()->create([
            "bank_id" => 21,
            "account_number" => "000000000000",
            "holder_name" => $companyEmployee->first_name . " " . $companyEmployee->last_name
        ]);

        $this->call(RolePermissionSeeder::class);

        $super_admin_role = Role::where('name', 'super_admin')->first();
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@quantum.com',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole($super_admin_role);

        $admin_role = Role::where('name', 'admin')->first();
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@company.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($admin_role);
        $companyAdmin->update([
            'user_id' => $admin->id,
        ]);

        $employee_role = Role::where('name', 'employee')->first();
        $employee = User::create([
            'name' => 'Employee',
            'email' => 'employee@company.com',
            'password' => bcrypt('password'),
        ]);
        $employee->assignRole($employee_role);
        $companyEmployee->update([
            'user_id' => $employee->id,
        ]);
        $companyEmployee->salaryItems()->create([
            'salary_type_id' => 1,
            'amount' => 500,
        ]);
        $companyEmployee->salaryItems()->create([
            'salary_type_id' => 2,
            'amount' => 50,
        ]);
        $companyEmployee->salaryItems()->create([
            'salary_type_id' => 3,
            'amount' => 150,
        ]);
        $companyEmployee->salaryItems()->create([
            'salary_type_id' => 4,
            'amount' => 100,
        ]);
    }
}
