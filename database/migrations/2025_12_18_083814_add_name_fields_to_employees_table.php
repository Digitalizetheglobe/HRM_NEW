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
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'first_name')) {
                    $table->string('first_name')->nullable()->after('name');
                }
                if (!Schema::hasColumn('employees', 'middle_name')) {
                    $table->string('middle_name')->nullable()->after('first_name');
                }
                if (!Schema::hasColumn('employees', 'last_name')) {
                    $table->string('last_name')->nullable()->after('middle_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('employees', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
            if (Schema::hasColumn('employees', 'last_name')) {
                $table->dropColumn('last_name');
            }
        });
    }
};
