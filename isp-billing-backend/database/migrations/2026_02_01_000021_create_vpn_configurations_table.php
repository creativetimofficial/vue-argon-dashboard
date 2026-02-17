<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vpn_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained()->onDelete('cascade');
            
            $table->string('vpn_type', 50); // openvpn, wireguard, l2tp
            $table->string('server_address', 255);
            $table->integer('server_port');
            $table->text('config_file')->nullable();
            $table->text('api_key_encrypted')->nullable();
            $table->text('api_endpoint')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vpn_configurations');
    }
};
