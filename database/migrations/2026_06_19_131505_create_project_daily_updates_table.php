<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectDailyUpdatesTable extends Migration
{
    public function up()
    {
        Schema::create('project_daily_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->date('work_date');
            $table->text('work_done');
            $table->decimal('hours_worked', 8, 2);
            $table->integer('progress_before')->default(0);
            $table->integer('progress_after')->default(0);
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_daily_updates');
    }
}
