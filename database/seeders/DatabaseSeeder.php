<?php

namespace Database\Seeders;

use App\Models\HRM\Bank;
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
        //Create banks
        $bank = Bank::insert([
            ["name" => "AGROBANK", "code" => "AGOBMYKL"],
            ["name" => "MBSB BANK BERHAD", "code" => "AFBQMYKL"],
            ["name" => "AMBANK BERHAD", "code" => "ARBKMYKL"],
            ["name" => "BANK ISLAM MALAYSIA BERHAD", "code" => "BIMBMYKL"],
            ["name" => "BANK OF CHINA (MALAYSIA) BERHAD", "code" => "BKCHMYKL"],
            ["name" => "BANGKOK BANK BERHAD", "code" => "BKKBMYKL"],
            ["name" => "BANK KERJASAMA RAKYAT MALAYSIA BERHAD", "code" => "BKRMMYKL"],
            ["name" => "BANK MUAMALAT MALAYSIA BERHAD", "code" => "BMMBMYKL"],
            ["name" => "BNP PARIBAS MALAYSIA BERHAD", "code" => "BNPAMYKL"],
            ["name" => "BANK OF AMERICA (MALAYSIA) BERHAD", "code" => "BOFAMY2X"],
            ["name" => "MUFG BANK (MALAYSIA) BERHAD", "code" => "BOTKMYKX"],
            ["name" => "BANK SIMPANAN NASIONAL", "code" => "BSNAMYK1"],
            ["name" => "J.P. MORGAN CHASE BANK BERHAD", "code" => "CHASMYKX"],
            ["name" => "CIMB BANK BERHAD", "code" => "CIBBMYKL"],
            ["name" => "CITIBANK BERHAD", "code" => "CITIMYKL"],
            ["name" => "DEUTSCHE BANK (MALAYSIA) BERHAD", "code" => "DEUTMYKL"],
            ["name" => "HSBC BANK MALAYSIA BERHAD", "code" => "HBMBMYKL"],
            ["name" => "HONG LEONG BANK BERHAD", "code" => "HLBBMYKL"],
            ["name" => "INDUSTRIAL & COMM. BANK OF CHINA BERHAD", "code" => "ICBKMYKL"],
            ["name" => "KUWAIT FINANCE HOUSE (MALAYSIA) BERHAD", "code" => "KFHOMYKL"],
            ["name" => "MAYBANK BERHAD", "code" => "MBBEMYKL"],
            ["name" => "ALLIANCE BANK MALAYSIA BERHAD", "code" => "MFBBMYKL"],
            ["name" => "MIZUHO BANK (MALAYSIA) BERHAD", "code" => "MHCBMYKA"],
            ["name" => "OCBC BANK (MALAYSIA) BERHAD", "code" => "OCBCMYKL"],
            ["name" => "PUBLIC BANK BERHAD", "code" => "PBBEMYKL"],
            ["name" => "CHINA CONSTRUCTION BANK (M) BERHAD", "code" => "PCBCMYKL"],
            ["name" => "AFFIN BANK BERHAD", "code" => "PHBMMYKL"],
            ["name" => "RHB BANK BERHAD", "code" => "RHBBMYKL"],
            ["name" => "AL-RAJHI BANKING & INVESTMENT CORP", "code" => "RJHIMYKL"],
            ["name" => "STANDARD CHARTERED BANK MALAYSIA BERHAD", "code" => "SCBLMYKX"],
            ["name" => "SUMITOMO MITSUI BANKING CORP MSIA BHD", "code" => "SMBCMYKL"],
            ["name" => "UNITED OVERSEAS BANK MALAYSIA BERHAD", "code" => "UOVBMYKL"],
            ["name" => "THE ROYAL BANK OF SCOTLAND BERHAD","code" =>  "ABNAMYKL"],
            ["name" => "AFFIN ISLAMIC BANK BERHAD","code" =>  "AIBBMYKL"],
            ["name" => "AMBANK ISLAMIC BERHAD","code" =>  "AISLMYKL"],
            ["name" => "ALLIANCE ISLAMIC BANK BERHAD","code" =>  "ALSRMYKL"],
            ["name" => "AMINVESTMENT BANK BHD - SPI","code" =>  "AMMBMY21"],
            ["name" => "AMINVESTMENT BANK BERHAD","code" =>  "AMMBMYKL"],
            ["name" => "BURSA MALAYSIA DERIVATIVES CLEARING BHD","code" =>  "BMDRMYK1"],
            ["name" => "BURSA MALAYSIA SECURITIES BERHAD","code" =>  "BMSCMYK1"],
            ["name" => "BANK NEGARA MALAYSIA","code" =>  "BNMAMYKL"],
            ["name" => "BNP PARIBAS MALAYSIA BERHAD - SPI","code" =>  "BNPAMY21"],
            ["name" => "MUFG BANK (MALAYSIA) BERHAD - SPI","code" =>  "BOTKMY21"],
            ["name" => "BANK SIMPANAN NASIONAL - SPI","code" =>  "BSNAMY21"],
            ["name" => "CAGAMAS BERHAD - SPI", "code" => "CAGAMY21"],
            ["name" => "CAGAMAS BERHAD", "code" => "CAGAMYKL"],
            ["name" => "CITIBANK BERHAD - SPI", "code" => "CITIMYM1"],
            ["name" => "CIMB INVESTMENT BANK BERHAD - SPI", "code" => "COIMMY21"],
            ["name" => "CIMB INVESTMENT BANK BERHAD", "code" => "COIMMYKL"],
            ["name" => "CIMB ISLAMIC BANK BERHAD", "code" => "CTBBMYKL"],
            ["name" => "DEUTSCHE BANK MALAYSIA BERHAD - SPI", "code" => "DEUTMY21"],
            ["name" => "EXPORT IMPORT BANK MALAYSIA BERHAD - SPI", "code" => "EXMBMY21"],
            ["name" => "EXPORT IMPORT BANK MALAYSIA BERHAD", "code" => "EXMBMYKL"],
            ["name" => "AFFIN HWANG INVESTMENT BANK BERHAD", "code" => "HDSBMY2P"],
            ["name" => "HONG LEONG ISLAMIC BANK BERHAD", "code" => "HLIBMYKL"],
            ["name" => "HONG LEONG INVESTMENT BANK BERHAD", "code" => "HLIVMYKL"],
            ["name" => "HSBC AMANAH MALAYSIA BERHAD", "code" => "HMABMYKL"],
            ["name" => "INDIA INTERNATIONAL BANK (M) BERHAD", "code" => "IIMBMYKL"],
            ["name" => "KAF INVESTMENT BANK BERHAD - SPI", "code" => "KAFBMY21"],
            ["name" => "KAF INVESTMENT BANK BERHAD", "code" => "KAFBMYKL"],
            ["name" => "CO-OP BANK PERTAMA MALAYSIA BERHAD", "code" => "KCPMMYK1"],
            ["name" => "KENANGA INVESTMENT BANK BERHAD - SPI", "code" => "KKENMY21"],
            ["name" => "KENANGA INVESTMENT BANK BERHAD", "code" => "KKENMYK1"],
            ["name" => "KUMPULAN WANG PERSARAAN (DIPERBADANKAN)", "code" => "KWAPMYK1"],
            ["name" => "KUMPULAN WANG SIMPANAN PEKERJA", "code" => "KWSPMYK1"],
            ["name" => "ALLIANCE INVESTMENT BANK BERHAD - SPI", "code" => "MBAMMY21"],
            ["name" => "ALLIANCE INVESTMENT BANK BERHAD", "code" => "MBAMMYKL"],
            ["name" => "MAYBANK INVESTMENT BANK BERHAD - SPI", "code" => "MBEAMY21"],
            ["name" => "MAYBANK INVESTMENT BANK BERHAD", "code" => "MBEAMYKL"],
            ["name" => "MAYBANK ISLAMIC BANK BERHAD", "code" => "MBISMYKL"],
            ["name" => "BURSA MALAYSIA DEPOSITY SDN BHD", "code" => "MCDSMYK1"],
            ["name" => "PAYMENTS NETWORK MALAYSIA SDN BHD", "code" => "MEPSMYK1"],
            ["name" => "EUROCLEAR BANK", "code" => "MGTCBEBE"],
            ["name" => "MIDF AMANAH INVESTMENT BANK BERHAD - SPI", "code" => "MIDFMY21"],
            ["name" => "MIDF AMANAH INVESTMENT BANK BERHAD", "code" => "MIDFMYK1"],
            ["name" => "SME BANK MALAYSIA BERHAD - SPI", "code" => "MSMEMY21"],
            ["name" => "SME BANK MALAYSIA BERHAD", "code" => "MSMEMYK1"],
            ["name" => "THE BANK OF NOVA SCOTIA BERHAD", "code" => "NOSCMYKL"],
            ["name" => "OCBC AL- AMIN BANK BERHAD", "code" => "OABBMYKL"],
            ["name" => "RHB INVESTMENT BANK BERHAD", "code" => "OSKIMYKL"],
            ["name" => "AFFIN INVESTMENT BANK BERHAD", "code" => "PAMBMYK1"],
            ["name" => "BANK PEMBANGUNAN MALAYSIA BERHAD - SPI", "code" => "PEMBMY21"],
            ["name" => "BANK PEMBANGUNAN MALAYSIA BERHAD", "code" => "PEMBMYK1"],
            ["name" => "PUBLIC ISLAMIC BANK BERHAD", "code" => "PIBEMYK1"],
            ["name" => "RHB ISLAMIC BANK", "code" => "RHBAMYKL"],
            ["name" => "STANDARD CHARTERED SAADIQ BANK BERHAD", "code" => "SCSRMYKK"],
            ["name" => "PUBLIC INVESTMENT BANK BERHAD", "code" => "SMBBMYK1"],
            ["name" => "UNITED OVERSEAS BANK MALAYSIA BHD - SPI", "code" => "UOVBMY21"],
        ]);

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
            "basic_salary" => 2000.50,
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
        ]);

        //Create employee bank account
        $employee->bankAccount()->create([
            "bank_id" => 21,
            "account_number" => "1234567890",
            "holder_name" => $employee->first_name . " " . $employee->last_name
        ]);
        

        // Create super admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@quantum.com',
            'password' => bcrypt('password'),
        ]);

        // Create salary types
        SalaryType::create(['name' => 'Allowance', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Deduction', 'type' => 'deduction']);
        SalaryType::create(['name' => 'Overtime', 'type' => 'allowance']);
        SalaryType::create(['name' => 'Company Contribution', 'type' => 'company_contribution']);
    }
}
