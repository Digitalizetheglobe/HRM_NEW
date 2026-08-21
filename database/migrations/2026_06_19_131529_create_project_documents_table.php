<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_documents');
    }
}
