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
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'custom_id')) $table->string('custom_id')->nullable();
            if (!Schema::hasColumn('employees', 'office_phone_one')) $table->string('office_phone_one')->nullable();
            if (!Schema::hasColumn('employees', 'office_phone_two')) $table->string('office_phone_two')->nullable();
            if (!Schema::hasColumn('employees', 'biometric_emp_id')) $table->string('biometric_emp_id')->nullable();
            if (!Schema::hasColumn('employees', 'site_id')) $table->unsignedBigInteger('site_id')->nullable();
            if (!Schema::hasColumn('employees', 'education_details')) $table->json('education_details')->nullable();
            if (!Schema::hasColumn('employees', 'experience_details')) $table->json('experience_details')->nullable();
            if (!Schema::hasColumn('employees', 'project_id')) $table->unsignedBigInteger('project_id')->nullable();
            if (!Schema::hasColumn('employees', 'week_off_day')) $table->string('week_off_day')->nullable();
            if (!Schema::hasColumn('employees', 'education_images')) $table->json('education_images')->nullable();
            if (!Schema::hasColumn('employees', 'approval_status')) $table->string('approval_status')->nullable()->default('pending');
            if (!Schema::hasColumn('employees', 'approved_at')) $table->timestamp('approved_at')->nullable();
            if (!Schema::hasColumn('employees', 'approved_by')) $table->unsignedBigInteger('approved_by')->nullable();
            if (!Schema::hasColumn('employees', 'rejection_reason')) $table->text('rejection_reason')->nullable();
            if (!Schema::hasColumn('employees', 'comp_off_enabled')) $table->boolean('comp_off_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columnsToDrop = [
                'custom_id',
                'office_phone_one',
                'office_phone_two',
                'site_id',
                'education_details',
                'experience_details',
                'project_id',
                'week_off_day',
                'education_images',
                'approval_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
                'comp_off_enabled'
            ];
            if (Schema::hasColumn('employees', 'biometric_emp_id')) {
                $columnsToDrop[] = 'biometric_emp_id';
            }
            $table->dropColumn($columnsToDrop);
        });
    }
};
