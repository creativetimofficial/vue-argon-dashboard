<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IspTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'isp_id',
        'name',
        // Original fields from create migration
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'text_color',
        'font_family',
        'heading_font',
        'sidebar_position',
        'dark_mode',
        'logo_url',
        'logo_dark_url',
        'favicon_url',
        'custom_css',
        'is_active',
        'is_default',
        // New extended theme fields from add migration
        'button_primary_color',
        'button_secondary_color',
        'link_color',
        'text_primary_color',
        'text_secondary_color',
        'sidebar_bg_color',
        'navbar_bg_color',
        'dashboard_bg_color',
        'login_bg_image',
        'register_bg_image',
        'use_custom_logo',
        'company_name',
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'use_custom_logo' => 'boolean',
    ];

    public function isp()
    {
        return $this->belongsTo(ISP::class);
    }
}