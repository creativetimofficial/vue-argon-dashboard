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
        'primary_color',
        'secondary_color',
        'sidebar_color',
        'sidebar_text_color',
        'header_color',
        'header_text_color',
        'logo_url',
        'favicon_url',
        'custom_css',
        'custom_js',
        'is_dark_mode',
        'show_company_name',
        'show_powered_by',
        'font_family',
        'border_radius',
        'button_style',
        'card_shadow',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_dark_mode' => 'boolean',
        'show_company_name' => 'boolean',
        'show_powered_by' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function isp()
    {
        return $this->belongsTo(ISP::class);
    }
}