<?php

namespace Database\Seeders;

use App\Models\HRM\Bank;
use App\Models\HRM\ClaimType;
use App\Models\HRM\LeaveType;
use App\Models\Salary\SalaryType;
use App\Models\Sales\SaleStatus;
use Illuminate\Database\Seeder;

class StaticDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
            ["name" => "THE ROYAL BANK OF SCOTLAND BERHAD", "code" =>  "ABNAMYKL"],
            ["name" => "AFFIN ISLAMIC BANK BERHAD", "code" =>  "AIBBMYKL"],
            ["name" => "AMBANK ISLAMIC BERHAD", "code" =>  "AISLMYKL"],
            ["name" => "ALLIANCE ISLAMIC BANK BERHAD", "code" =>  "ALSRMYKL"],
            ["name" => "AMINVESTMENT BANK BHD - SPI", "code" =>  "AMMBMY21"],
            ["name" => "AMINVESTMENT BANK BERHAD", "code" =>  "AMMBMYKL"],
            ["name" => "BURSA MALAYSIA DERIVATIVES CLEARING BHD", "code" =>  "BMDRMYK1"],
            ["name" => "BURSA MALAYSIA SECURITIES BERHAD", "code" =>  "BMSCMYK1"],
            ["name" => "BANK NEGARA MALAYSIA", "code" =>  "BNMAMYKL"],
            ["name" => "BNP PARIBAS MALAYSIA BERHAD - SPI", "code" =>  "BNPAMY21"],
            ["name" => "MUFG BANK (MALAYSIA) BERHAD - SPI", "code" =>  "BOTKMY21"],
            ["name" => "BANK SIMPANAN NASIONAL - SPI", "code" =>  "BSNAMY21"],
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

        //Create leave types
        LeaveType::insert([
            ["name" => "Sick Leave"],
            ["name" => "Annual Leave"],
            ["name" => "Maternity Leave"],
            ["name" => "Paternity Leave"],
            ["name" => "Emergency Leave"],
            ["name" => "Unpaid Leave"],
            ["name" => "Compassionate Leave"],
        ]);

        //Create salary types
        SalaryType::insert([
            ["name" => "Allowance", "type" => "allowance"],
            ["name" => "Deduction", "type" => "deduction"],
            ["name" => "Overtime", "type" => "allowance"],
            ["name" => "Company Contribution", "type" => "company_contribution"],
        ]);

        //Create claim types
        ClaimType::insert([
            ["name" => "Travel Expenses"],
            ["name" => "Office Supplies"],
            ["name" => "Equipment"],
            ["name" => "Training"],
            ["name" => "Medical"],
            ["name" => "Entertainment"],
            ["name" => "Software"],
            ["name" => "Others"],
        ]);

        //Create sale statuses
        SaleStatus::insert([
            ["type" => "quotation_invoice", "name" => "Draft"],
            ["type" => "quotation", "name" => "Sent"],
            ["type" => "quotation", "name" => "Approved"],
            ["type" => "quotation", "name" => "Rejected"],
            ["type" => "quotation", "name" => "Completed"],
            ["type" => "quotation_invoice", "name" => "Cancelled"],
            ["type" => "invoice", "name" => "Waiting for Payment"],
            ["type" => "invoice", "name" => "Paid"],
            ["type" => "invoice", "name" => "Overdue"],
            ["type" => "invoice", "name" => "Partially Paid"],
            ["type" => "invoice", "name" => "Unpaid"],
        ]);
    }
}
