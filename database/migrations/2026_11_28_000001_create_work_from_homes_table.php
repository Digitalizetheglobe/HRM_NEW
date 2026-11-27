<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('work_from_homes')) {
            Schema::create('work_from_homes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('start_date')->index();
                $table->date('end_date')->index();
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
        Schema::dropIfExists('work_from_homes');
    }
};

