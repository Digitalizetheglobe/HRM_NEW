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
        Schema::create('design_version_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_version_id');
            $table->string('title');
            $table->string('url');
            $table->timestamps();

            $table->foreign('design_version_id')->references('id')->on('design_versions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_version_links');
    }
};
