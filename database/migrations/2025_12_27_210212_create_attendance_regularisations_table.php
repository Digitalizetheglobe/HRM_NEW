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
        Schema::create('attendance_regularisations', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->date('missed_attendance_date');
            $table->time('punch_in_time');
            $table->time('punch_out_time');
            $table->enum('reason', ['Missed Punch', 'Technical Error', 'Others'])->default('Missed Punch');
            $table->text('remark')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->integer('created_by');
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_regularisations');
    }
};
