<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SubscriptionPackageSeeder::class,
            UserSeeder::class,
            ISPSeeder::class,
            PaymentGatewaySeeder::class,
        ]);
    }
}
