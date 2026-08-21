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
        if (!Schema::hasColumn('leaves', 'half_day_session')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->enum('half_day_session', ['first_half', 'second_half'])->nullable()->after('leave_duration_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('half_day_session');
        });
    }
};










