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
        // Drop the comp_off_leaves table if it exists
        Schema::dropIfExists('comp_off_leaves');

        // Remove comp_off_enabled column from employees table if it exists
        if (Schema::hasColumn('employees', 'comp_off_enabled')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('comp_off_enabled');
            });
        }
        
        // Remove Comp-Off leave type if it exists
        \DB::table('leave_types')->where('title', 'Comp-Off')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the comp_off_enabled column
        if (!Schema::hasColumn('employees', 'comp_off_enabled')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->boolean('comp_off_enabled')->default(true);
            });
        }

        // Re-create the comp_off_leaves table
        Schema::create('comp_off_leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('employees_id');
            $table->date('comp_off_date');
            $table->timestamps();
        });
    }
};
