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
            if (! Schema::hasColumn('projects', 'location')) { $table->string('location')->nullable(); }
            if (! Schema::hasColumn('projects', 'assigned_data')) { $table->json('assigned_data')->nullable(); }
            if (! Schema::hasColumn('projects', 'site_heads')) { $table->json('site_heads')->nullable(); }
            if (! Schema::hasColumn('projects', 'created_by')) { $table->unsignedBigInteger('created_by')->nullable(); }
            if (! Schema::hasColumn('projects', 'technology')) { $table->string('technology')->nullable(); }
            if (! Schema::hasColumn('projects', 'delay_reason')) { $table->text('delay_reason')->nullable(); }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'assigned_data',
                'site_heads',
                'created_by',
                'technology',
                'delay_reason',
            ]);
        });
    }
};
