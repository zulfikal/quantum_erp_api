<?php

namespace Database\Seeders;

use App\Models\HRM\Company;
use Illuminate\Database\Seeder;
use Faker\Factory;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();

        for ($i = 1; $i <= 50; $i++) {
            $entity = $company->entities()->create([
                "name" => Factory::create()->name,
                "type" => Factory::create()->randomElement(['customer', 'supplier']),
                "created_by" => Factory::create()->numberBetween(10, 20),
                "entity_id" => Factory::create()->numerify('##########'),
                "tin_number" => Factory::create()->numerify('##########'),
                "status" => Factory::create()->randomElement(['active', 'inactive']),
                "website" => Factory::create()->url,
                "notes" => Factory::create()->text,
            ]);

            $entity->addresses()->create([
                "address_1" => Factory::create()->streetAddress,
                "address_2" => Factory::create()->streetName,
                "city" => Factory::create()->city,
                "state" => Factory::create()->state,
                "zip_code" => Factory::create()->postcode,
                "country" => Factory::create()->country,
                "notes" => Factory::create()->text,
                "type" => Factory::create()->randomElement(['billing', 'shipping', 'billing_and_shipping']),
                "is_default" => true,
            ]);

            $entity->contacts()->create([
                "type" => "email",
                "value" => Factory::create()->email,
                "notes" => Factory::create()->text,
            ]);

            $entity->contacts()->create([
                "type" => "phone",
                "value" => Factory::create()->phoneNumber,
                "notes" => Factory::create()->text,
            ]);
        }
    }
}
