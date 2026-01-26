<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_code',
        'invoice_id',
        'isp_id',
        'amount',
        'payment_method',
        'gateway_id',
        'gateway_transaction_id',
        'gateway_response',
        'payment_proof',
        'payment_note',
        'received_by',
        'status',
        'payment_date',
        'confirmed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'payment_date' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }
}
