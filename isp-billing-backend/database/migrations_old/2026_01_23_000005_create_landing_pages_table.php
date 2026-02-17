<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained('isps')->onDelete('cascade');

            // Hero Section
            $table->string('hero_title', 255)->default('Welcome to ISP Billing');
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_cta_text', 50)->default('Get Started');
            $table->string('hero_cta_link', 255)->default('/register');

            // Features Section
            $table->boolean('show_features')->default(true);
            $table->text('features')->nullable(); // JSON

            // Pricing Section
            $table->boolean('show_pricing')->default(true);

            // Testimonials
            $table->boolean('show_testimonials')->default(false);
            $table->text('testimonials')->nullable(); // JSON

            // Contact Section
            $table->boolean('show_contact')->default(true);
            $table->text('contact_info')->nullable(); // JSON

            // SEO
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Custom Content
            $table->longText('custom_html')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
