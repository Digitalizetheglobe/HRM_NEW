<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePolicyAcknowledgementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('policy_acknowledgements')) {
            Schema::create('policy_acknowledgements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_policy_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('user_id'); // For quick access to user
                $table->boolean('has_previewed')->default(false);
                $table->boolean('has_downloaded')->default(false);
                $table->timestamp('previewed_at')->nullable();
                $table->timestamp('downloaded_at')->nullable();
                $table->timestamp('acknowledged_at')->nullable();
                $table->text('ip_address')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('company_policy_id')->references('id')->on('company_policies')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // Unique constraint: one acknowledgement per employee per policy
                $table->unique(['company_policy_id', 'employee_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policy_acknowledgements');
    }
}










