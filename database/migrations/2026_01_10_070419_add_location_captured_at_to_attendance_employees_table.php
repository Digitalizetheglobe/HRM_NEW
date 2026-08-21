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
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->timestamp('clock_in_location_captured_at')->nullable()->after('clock_in_accuracy');
            $table->timestamp('clock_out_location_captured_at')->nullable()->after('clock_out_accuracy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn(['clock_in_location_captured_at', 'clock_out_location_captured_at']);
        });
    }
};
