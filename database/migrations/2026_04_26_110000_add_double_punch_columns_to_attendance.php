<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->time('clock_in_2')->nullable()->after('clock_out');
            $table->time('clock_out_2')->nullable()->after('clock_in_2');
            
            // Location for clock_in_2
            $table->string('clock_in_2_latitude')->nullable();
            $table->string('clock_in_2_longitude')->nullable();
            $table->string('clock_in_2_location')->nullable();
            $table->string('clock_in_2_accuracy')->nullable();
            $table->timestamp('clock_in_2_location_captured_at')->nullable();
            
            // Location for clock_out_2
            $table->string('clock_out_2_latitude')->nullable();
            $table->string('clock_out_2_longitude')->nullable();
            $table->string('clock_out_2_location')->nullable();
            $table->string('clock_out_2_accuracy')->nullable();
            $table->timestamp('clock_out_2_location_captured_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_2',
                'clock_out_2',
                'clock_in_2_latitude',
                'clock_in_2_longitude',
                'clock_in_2_location',
                'clock_in_2_accuracy',
                'clock_in_2_location_captured_at',
                'clock_out_2_latitude',
                'clock_out_2_longitude',
                'clock_out_2_location',
                'clock_out_2_accuracy',
                'clock_out_2_location_captured_at'
            ]);
        });
    }
};
