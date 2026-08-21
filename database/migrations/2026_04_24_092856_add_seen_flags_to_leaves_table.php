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
        Schema::table('leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('leaves', 'seen_by_manager')) {
                $table->boolean('seen_by_manager')->default(0);
            }
            if (!Schema::hasColumn('leaves', 'seen_by_director')) {
                $table->boolean('seen_by_director')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['seen_by_manager', 'seen_by_director']);
        });
    }
};
