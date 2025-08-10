<?php

namespace Database\Seeders;

use App\Models\HRM\Company;
use App\Models\Salary\SalaryType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Create companies
        $company = Company::create([
            "name" => "Quantum ERP SDN. BHD.",
            "register_number" => "A109333",
            "tin_number" => "109020020",
            "status" => "active"
        ]);

        //Create departments
        $department = $company->departments()->create([
            "name" => "IT",
        ]);

        //Create designations
        $name = "Software Engineer";
        $code = str_replace(" ", "_", strtolower($name));
        $designation = $company->designations()->create([
            "name" => $name,
            "status" => "active",
            "code" => $code
        ]);

        //Create company branches
        $companyBranch = $company->branches()->create([
            "name" => "Main Branch",
            "address_1" => "Main Branch Address",
            "company_id" => $company->id,
            "city" => "Temerloh",
            "state" => "Pahang",
            "zip_code" => "28000",
            "country" => "Malaysia",
            "phone" => "+60123456789"
        ]);

        //Create employees
        $employee = $companyBranch->employees()->create([
            "user_id" => null,
            "designation_id" => $designation->id,
            "department_id" => $department->id,
            "nric_number" => "1234567890",
            "first_name" => "John",
            "last_name" => "Doe 1",
            "email" => "john.doe1@example.com",
            "phone" => "1234567890",
            "gender" => "male",
            "marital_status" => "single",
            "nationality" => "Malaysia",
            "religion" => "Islam",
            "address_1" => "123 Main St",
            "city" => "New York",
            "state" => "New York",
            "zip_code" => "10001",
            "country" => "Malaysia",
            "register_number" => "1234567890",
            "bank_name" => "Maybank",
            "bank_account_number" => "1234567890"
        ]);
        

        // Create super admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@quantum.com',
            'password' => bcrypt('password'),
        ]);

        // Create salary types
        SalaryType::create(['name' => 'Basic Salary', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Allowance', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Deduction', 'type' => 'deduction']);
        SalaryType::create(['name' => 'Overtime', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Company Contribution', 'type' => 'company_contribution']);
    }
}
