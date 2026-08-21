<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryProcessingStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_processing_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('employee_id');
            $table->string('year');
            $table->string('month');
            $table->string('status')->default('Pending'); // Pending, Done
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate entries for same employee/month/year
            $table->unique(['employee_id', 'year', 'month'], 'unique_employee_month_year');
            
            // Index for faster queries
            $table->index(['year', 'month']);
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_processing_status');
    }
}
