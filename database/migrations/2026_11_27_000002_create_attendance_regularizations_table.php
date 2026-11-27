<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendance_regularizations')) {
            Schema::create('attendance_regularizations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('date')->index();
                $table->time('clock_in');
                $table->time('clock_out');
                $table->text('reason');
                $table->string('status', 20)->default('Pending')->index();
                $table->unsignedBigInteger('action_by')->nullable();
                $table->timestamp('action_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('created_by')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_regularizations');
    }
};

