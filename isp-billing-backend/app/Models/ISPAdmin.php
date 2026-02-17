<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ISPAdmin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'isp_admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'company_address',
        'subdomain',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
