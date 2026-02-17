<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_name',
        'slug',
        'gateway_type',
        'is_active',
        'is_default',
        'api_key',
        'secret_key',
        'merchant_id',
        'client_id',
        'settings',
        'supported_countries',
        'transaction_fee',
        'fixed_fee',
        'currency',
        'webhook_secret',
        'webhook_url',
        'sandbox_mode',
        'expiry_duration',
        'logo_url',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'settings' => 'array',
        'supported_countries' => 'array',
        'transaction_fee' => 'decimal:2',
        'fixed_fee' => 'decimal:2',
        'sandbox_mode' => 'boolean',
        'expiry_duration' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $hidden = [
        'secret_key',
        'webhook_secret',
    ];
}