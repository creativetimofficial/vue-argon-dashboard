<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('landing_pages', 'show_logos')) {
                $table->boolean('show_logos')->default(true)->after('show_testimonials');
            }
            if (!Schema::hasColumn('landing_pages', 'logos')) {
                $table->text('logos')->nullable()->after('show_logos');
            }
            if (!Schema::hasColumn('landing_pages', 'show_faqs')) {
                $table->boolean('show_faqs')->default(true)->after('logos');
            }
            if (!Schema::hasColumn('landing_pages', 'faqs')) {
                $table->text('faqs')->nullable()->after('show_faqs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['show_logos', 'logos', 'show_faqs', 'faqs']);
        });
    }
};
