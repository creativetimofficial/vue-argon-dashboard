<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'isp_id',
        'name',
        'phone',
        'avatar',
        'employee_id',
        'specialization',
        'coverage_area',
        'status',
        'current_location',
    ];

    protected $casts = [
        'current_location' => 'array',
    ];

    /**
     * Get the user record associated with the technician.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Get the ISP that owns the technician.
     */
    public function isp()
    {
        return $this->belongsTo(ISP::class);
    }

    /**
     * Get installation requests assigned to technician.
     */
    public function installationRequests()
    {
        return $this->hasMany(InstallationRequest::class, 'assigned_technician_id');
    }

    /**
     * Get repair tickets assigned to technician.
     */
    public function repairTickets()
    {
        return $this->hasMany(RepairTicket::class, 'assigned_technician_id');
    }

    /**
     * Check if technician is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Scope for available technicians.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for specific ISP.
     */
    public function scopeForISP($query, $ispId)
    {
        return $query->where('isp_id', $ispId);
    }
}
