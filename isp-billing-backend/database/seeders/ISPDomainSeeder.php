<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ISP;
use Illuminate\Support\Str;

class ISPDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test ISP with subdomain
        ISP::create([
            'company_name' => 'Test ISP - ASDFG',
            'email' => 'admin@asdfg.test',
            'subdomain' => 'asdfg',
            'phone' => '08123456789',
            'address' => 'Jl. Test No. 123',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'subscription_status' => 'active',
            'approval_status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
        ]);

        echo "✅ ISP with subdomain 'asdfg' created successfully!\n";
        echo "   Access: http://asdfg.localhost or https://asdfg.localhost\n";
    }
}
