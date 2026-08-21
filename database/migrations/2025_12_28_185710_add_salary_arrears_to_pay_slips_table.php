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
        Schema::table('pay_slips', function (Blueprint $table) {
            $table->float('salary_arrears', 15, 2)->default(0)->after('overtime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pay_slips', function (Blueprint $table) {
            $table->dropColumn('salary_arrears');
        });
    }
};
