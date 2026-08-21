<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectScreenshotsTable extends Migration
{
    public function up()
    {
        Schema::create('project_screenshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('image');
            $table->string('caption')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_screenshots');
    }
}
