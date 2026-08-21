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
        Schema::table('project_daily_updates', function (Blueprint $table) {
            $table->dropColumn(['progress_before', 'progress_after']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_daily_updates', function (Blueprint $table) {
            $table->decimal('progress_before', 5, 2)->default(0);
            $table->decimal('progress_after', 5, 2)->default(0);
        });
    }
};
