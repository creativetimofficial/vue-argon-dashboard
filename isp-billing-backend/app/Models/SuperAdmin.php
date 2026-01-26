<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'avatar',
    ];

    /**
     * Get the user record associated with the super admin.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Get all ISPs (for super admin view).
     */
    public function isps()
    {
        return ISP::query();
    }
}
