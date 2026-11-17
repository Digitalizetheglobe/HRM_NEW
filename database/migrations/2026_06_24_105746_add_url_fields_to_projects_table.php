<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'has_urls')) { $table->boolean('has_urls')->default(false)->after('status'); }
            if (! Schema::hasColumn('projects', 'website_url')) { $table->string('website_url')->nullable()->after('has_urls'); }
            if (! Schema::hasColumn('projects', 'dashboard_url')) { $table->string('dashboard_url')->nullable()->after('website_url'); }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['has_urls', 'website_url', 'dashboard_url']);
        });
    }
};
