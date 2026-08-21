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
        Schema::create('site_visits', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->integer('employee_id');
            $blueprint->date('date');
            $blueprint->string('location');
            $blueprint->string('status')->default('Pending');
            $blueprint->text('reason')->nullable();
            $blueprint->integer('approved_by')->nullable();
            $blueprint->integer('created_by');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_visits');
    }
};
