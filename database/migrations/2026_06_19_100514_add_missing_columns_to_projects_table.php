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
            $table->string('location')->nullable();
            $table->json('assigned_data')->nullable();
            $table->json('site_heads')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('technology')->nullable();
            $table->text('delay_reason')->nullable();
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
