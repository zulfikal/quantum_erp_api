<?php

namespace Database\Seeders;

use App\Models\HRM\Bank;
use App\Models\HRM\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $employee = Employee::create([
                'staff_id' => '0010100' . ($i + 1),
                "department_id" => Factory::create()->numberBetween(2, 9),
                "designation_id" => Factory::create()->numberBetween(2, 10),
                "company_branch_id" => 1,
                'nric_number' => Factory::create()->numerify('############'),
                'first_name' => Factory::create()->firstName,
                'last_name' => Factory::create()->lastName,
                'email' => Factory::create()->email,
                'phone' => Factory::create()->phoneNumber,
                'basic_salary' => 1500,
                'gender' => Factory::create()->randomElement(['male', 'female']),
                'marital_status' => Factory::create()->randomElement(['single', 'married', 'divorced']),
                'nationality' => 'Malaysia',
                'religion' => 'Other',
                'address_1' => Factory::create()->address,
                'city' => Factory::create()->city,
                'state' => Factory::create()->state,
                'zip_code' => Factory::create()->postcode,
                'country' => 'Malaysia',
                'register_number' => Factory::create()->numerify('##########'),
                'status' => 'active',
            ]);

            $employee->bankAccount()->create([
                "bank_id" => Factory::create()->randomElement(Bank::all()->pluck('id')->toArray()),
                "account_number" => Factory::create()->numerify('##########'),
                "holder_name" => $employee->first_name . " " . $employee->last_name
            ]);

            $employee->update([
                'user_id' => $employee->id,
            ]);
            $employee->salaryItems()->create([
                'salary_type_id' => 1,
                'amount' => 500,
            ]);
            $employee->salaryItems()->create([
                'salary_type_id' => 2,
                'amount' => 50,
            ]);
            $employee->salaryItems()->create([
                'salary_type_id' => 3,
                'amount' => 150,
            ]);
            $employee->salaryItems()->create([
                'salary_type_id' => 4,
                'amount' => 100,
            ]);

            $role = Role::where('name', 'employee')->first();

            $user = User::create([
                'name' => $employee->first_name . " " . $employee->last_name,
                'email' => $employee->email,
                'password' => bcrypt('password'),
            ]);

            $user->employee()->save($employee);

            $user->assignRole($role);
        }
    }
}
