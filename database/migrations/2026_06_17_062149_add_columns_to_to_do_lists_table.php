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
        Schema::table('to_do_lists', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('task');
            $table->date('end_date')->nullable()->after('start_date');
            $table->text('description')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('to_do_lists', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'description']);
        });
    }
};
