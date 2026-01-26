<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bank Transfer (Default Active)
        PaymentGateway::create([
            'name' => 'Bank Transfer (BCA)',
            'slug' => 'bank-transfer-bca',
            'provider' => 'bank_transfer',
            'is_active' => true,
            'settings' => [
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_holder' => 'PT. ISP Billing Indonesia',
                'instructions' => 'Silakan transfer ke rekening di atas dan upload bukti pembayaran.',
            ],
            'sort_order' => 1,
        ]);

        // 2. Midtrans (Inactive by default)
        PaymentGateway::create([
            'name' => 'Midtrans Payment Gateway',
            'slug' => 'midtrans',
            'provider' => 'midtrans',
            'is_active' => false,
            'settings' => [
                'merchant_id' => 'your-merchant-id',
                'client_key' => 'your-client-key',
                'server_key' => 'your-server-key',
                'is_production' => false,
            ],
            'sort_order' => 2,
        ]);
    }
}
