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
            $table->decimal('clock_in_latitude', 10, 8)->nullable()->after('clock_in');
            $table->decimal('clock_in_longitude', 11, 8)->nullable()->after('clock_in_latitude');
            $table->text('clock_in_location')->nullable()->after('clock_in_longitude');
            $table->decimal('clock_in_accuracy', 10, 2)->nullable()->after('clock_in_location');
            
            $table->decimal('clock_out_latitude', 10, 8)->nullable()->after('clock_out');
            $table->decimal('clock_out_longitude', 11, 8)->nullable()->after('clock_out_latitude');
            $table->text('clock_out_location')->nullable()->after('clock_out_longitude');
            $table->decimal('clock_out_accuracy', 10, 2)->nullable()->after('clock_out_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_latitude',
                'clock_in_longitude',
                'clock_in_location',
                'clock_in_accuracy',
                'clock_out_latitude',
                'clock_out_longitude',
                'clock_out_location',
                'clock_out_accuracy'
            ]);
        });
    }
};
