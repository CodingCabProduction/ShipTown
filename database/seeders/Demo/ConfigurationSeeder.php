<?php

namespace Database\Seeders\Demo;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        Configuration::updateOrCreate([], [
            'business_name' => '',
            'ecommerce_connected' => true,
            'disable_2fa' => true
        ]);
    }
}
