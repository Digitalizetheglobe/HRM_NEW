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
        Schema::create('design_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_version_id');
            $table->string('title');
            $table->string('feedback_type');
            $table->text('comment');
            $table->string('status')->default('Pending');
            $table->string('priority')->default('Medium');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('submitted_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();

            $table->foreign('design_version_id')->references('id')->on('design_versions')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_feedbacks');
    }
};
