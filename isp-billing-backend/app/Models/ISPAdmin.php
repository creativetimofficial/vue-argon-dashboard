<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISPAdmin extends Model
{
    use HasFactory;

    protected $table = 'isp_admins';

    protected $fillable = [
        'isp_id',
        'name',
        'phone',
        'avatar',
        'position',
        'is_primary',
        'permissions',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'permissions' => 'array',
    ];

    /**
     * Get the user record associated with the ISP admin.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Get the ISP that owns the admin.
     */
    public function isp()
    {
        return $this->belongsTo(ISP::class);
    }

    /**
     * Get customers for this ISP.
     */
    public function customers()
    {
        return $this->isp->customers();
    }

    /**
     * Get technicians for this ISP.
     */
    public function technicians()
    {
        return $this->isp->technicians();
    }

    /**
     * Check if admin has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->is_primary) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }
}
