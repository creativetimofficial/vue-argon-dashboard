<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'domain',
        'username',
        'password',
        'api_port',
        'vpn_local_address',
        'location',
        'is_active',
        'capacity',
        'current_users',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'current_users' => 'integer',
        'api_port' => 'integer',
    ];
}
