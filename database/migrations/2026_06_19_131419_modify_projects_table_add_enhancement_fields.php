<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyProjectsTableAddEnhancementFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_priority')->default('Medium');
            $table->decimal('estimated_hours', 10, 2)->default(0);
            $table->decimal('actual_hours', 10, 2)->default(0);
            $table->integer('project_progress')->default(0);
            $table->string('share_token')->nullable()->unique();
            $table->boolean('share_link_enabled')->default(false);
            $table->string('share_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'project_priority',
                'estimated_hours',
                'actual_hours',
                'project_progress',
                'share_token',
                'share_link_enabled',
                'share_password'
            ]);
        });
    }
}
