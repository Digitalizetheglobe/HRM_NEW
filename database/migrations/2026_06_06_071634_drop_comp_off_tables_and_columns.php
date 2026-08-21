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
        Schema::dropIfExists('comp_off_leave_logs');
        Schema::dropIfExists('comp_off_leaves');
        Schema::dropIfExists('comp_offs');

        if (Schema::hasColumn('employees', 'comp_off_enabled')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('comp_off_enabled');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
