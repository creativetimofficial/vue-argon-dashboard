<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'isp_id',
        'amount',
        'admin_fee',
        'total_transfer',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'xendit_disbursement_id',
        'notes',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_transfer' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function isp()
    {
        return $this->belongsTo(ISP::class);
    }
}
