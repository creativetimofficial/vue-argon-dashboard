<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISPService extends Model
{
    use HasFactory;

    protected $table = 'isp_services';

    protected $fillable = [
        'isp_id',
        'name',
        'slug',
        'description',
        'category',
        'price',
        'billing_cycle',
        'trial_days',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
        'requires_manual_approval',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'requires_manual_approval' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(ISPOrder::class, 'service_id');
    }
}
