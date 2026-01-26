<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            // Drop naming convention for foreign key if it exists
            if (Schema::hasColumn('payment_gateways', 'isp_id')) {
                // In some DBs we need to drop foreign key first
                try {
                    $table->dropForeign(['isp_id']);
                } catch (\Exception $e) {}
                
                $table->unsignedBigInteger('isp_id')->nullable()->change();
            }

            // Sync with model fields
            if (!Schema::hasColumn('payment_gateways', 'name')) {
                $table->string('name')->after('id')->nullable();
            }
            if (!Schema::hasColumn('payment_gateways', 'slug')) {
                $table->string('slug')->after('name')->nullable();
            }
            if (!Schema::hasColumn('payment_gateways', 'type')) {
                $table->enum('type', ['local', 'international', 'both'])->default('local')->after('slug');
            }
            if (!Schema::hasColumn('payment_gateways', 'api_key')) {
                $table->string('api_key')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('payment_gateways', 'secret_key')) {
                $table->string('secret_key')->nullable()->after('api_key');
            }
            if (!Schema::hasColumn('payment_gateways', 'merchant_id')) {
                $table->string('merchant_id')->nullable()->after('secret_key');
            }
            if (!Schema::hasColumn('payment_gateways', 'client_id')) {
                $table->string('client_id')->nullable()->after('merchant_id');
            }
            if (!Schema::hasColumn('payment_gateways', 'settings')) {
                $table->text('settings')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('payment_gateways', 'supported_countries')) {
                $table->text('supported_countries')->nullable()->after('settings');
            }
            if (!Schema::hasColumn('payment_gateways', 'transaction_fee')) {
                $table->decimal('transaction_fee', 8, 2)->default(0)->after('supported_countries');
            }
            if (!Schema::hasColumn('payment_gateways', 'fixed_fee')) {
                $table->decimal('fixed_fee', 12, 2)->default(0)->after('transaction_fee');
            }
            if (!Schema::hasColumn('payment_gateways', 'currency')) {
                $table->string('currency', 3)->default('IDR')->after('fixed_fee');
            }
            if (!Schema::hasColumn('payment_gateways', 'webhook_secret')) {
                $table->string('webhook_secret')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('payment_gateways', 'webhook_url')) {
                $table->string('webhook_url')->nullable()->after('webhook_secret');
            }
            if (!Schema::hasColumn('payment_gateways', 'sandbox_mode')) {
                $table->boolean('sandbox_mode')->default(true)->after('webhook_url');
            }
            if (!Schema::hasColumn('payment_gateways', 'logo_url')) {
                $table->string('logo_url')->nullable()->after('sandbox_mode');
            }
            if (!Schema::hasColumn('payment_gateways', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('logo_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            // We usually don't drop columns in down() to avoid data loss during experiments, 
            // but for completeness:
            // $table->dropColumn([...]);
        });
    }
};
