<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'isp_id',
        'invoice_id',
        'transaction_id',
        'payment_gateway',
        'payment_reference',
        'amount',
        'payment_method',
        'status',
        'payment_date',
        'paid_at',
        'payment_details',
        'notes',
        'expiry_time',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'payment_date' => 'datetime',
        'expiry_time' => 'datetime',
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
