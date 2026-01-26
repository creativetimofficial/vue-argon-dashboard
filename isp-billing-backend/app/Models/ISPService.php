<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISPService extends Model
{
    use HasFactory;

    protected $table = 'isp_services';

    protected $fillable = [
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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function orders()
    {
        return $this->hasMany(ISPOrder::class, 'service_id');
    }
}
