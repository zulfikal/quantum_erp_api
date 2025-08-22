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

        $company->productCategories()->create([
            "name" => "Electronics",
            "company_id" => $company->id,
            "description" => "Electronics products"
        ]);

        $company->productCategories()->create([
            "name" => "System Development",
            "company_id" => $company->id,
            "description" => "System Development products"
        ]);

        //Create admin department
        $department = $company->departments()->insert([
            [
                "company_id" => $company->id,
                "name" => "Admin"
            ],
            [
                "company_id" => $company->id,
                "name" => "IT"
            ],
            [
                "company_id" => $company->id,
                "name" => "Marketing"
            ],
            [
                "company_id" => $company->id,
                "name" => "Sales"
            ],
            [
                "company_id" => $company->id,
                "name" => "Finance"
            ],
            [
                "company_id" => $company->id,
                "name" => "Operations"
            ],
            [
                "company_id" => $company->id,
                "name" => "Logistics"
            ],
            [
                "company_id" => $company->id,
                "name" => "Marketing"
            ],
            [
                "company_id" => $company->id,
                "name" => "Support"
            ],
        ]);

        //Create designations
        $designation = $company->designations()->insert([
            [
                "company_id" => $company->id,
                "name" => "System Admin",
                "code" => "system_admin"
            ],
            [
                "company_id" => $company->id,
                "name" => "Employee",
                "code" => "employee"
            ],
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
            "phone" => "+60123456789",
            "email" => "info@quantum.com"
        ]);

        //Create admin employees
        $companyAdmin = $companyBranch->employees()->create([
            "user_id" => null,
            "staff_id" => "00101001",
            "designation_id" => 1,
            "department_id" => 1,
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
            "status" => "active",
        ]);

        //Create employees
        $companyEmployee = $companyBranch->employees()->create([
            "user_id" => null,
            "staff_id" => "00101002",
            "designation_id" => 2,
            "department_id" => 2,
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
            "status" => "active",
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
