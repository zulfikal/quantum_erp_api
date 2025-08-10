<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Salary\SalaryType;

class SalaryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalaryType::create(['name' => 'Basic Salary', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Allowance', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Deduction', 'type' => 'deduction']);
        SalaryType::create(['name' => 'Overtime', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Company Contribution', 'type' => 'company_contribution']);
    }
}
