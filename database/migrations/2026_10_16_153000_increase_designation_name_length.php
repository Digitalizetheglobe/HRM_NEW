<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow designation names of any practical length.
     */
    public function up(): void
    {
        if (Schema::hasTable('designations') && Schema::hasColumn('designations', 'name')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->text('name')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('designations') && Schema::hasColumn('designations', 'name')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->string('name', 255)->change();
            });
        }
    }
};
