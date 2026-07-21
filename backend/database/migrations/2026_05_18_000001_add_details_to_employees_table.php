<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Employee UID (auto-generated serial, e.g. #DTG001)
            $table->string('employee_uid')->nullable()->unique()->after('company_id');

            // Personal Details
            $table->string('phone')->nullable()->after('employee_uid');
            $table->date('dob')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('dob');        // male/female/other
            $table->text('address')->nullable()->after('gender');

            // Company Assignment
            $table->unsignedBigInteger('branch_id')->nullable()->after('address');
            $table->unsignedBigInteger('department_id')->nullable()->after('branch_id');
            $table->unsignedBigInteger('designation_id')->nullable()->after('department_id');
            $table->string('salary_type')->default('Monthly')->after('designation_id');
            $table->decimal('basic_salary', 12, 2)->default(0)->after('salary_type');
            $table->date('joining_date')->nullable()->after('basic_salary');
            $table->date('termination_date')->nullable()->after('joining_date');

            // Bank Details
            $table->string('account_holder_name')->nullable()->after('termination_date');
            $table->string('account_number')->nullable()->after('account_holder_name');
            $table->string('bank_name')->nullable()->after('account_number');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('ifsc_code')->nullable()->after('bank_branch');
            $table->string('pan_number')->nullable()->after('ifsc_code');

            // Document file paths
            $table->string('doc_aadhar_card')->nullable()->after('pan_number');
            $table->string('doc_pan_card')->nullable()->after('doc_aadhar_card');
            $table->string('doc_marksheet_10th')->nullable()->after('doc_pan_card');
            $table->string('doc_marksheet_12th')->nullable()->after('doc_marksheet_10th');
            $table->string('doc_degree_certificate')->nullable()->after('doc_marksheet_12th');
            $table->string('doc_experience_letter')->nullable()->after('doc_degree_certificate');
            $table->string('doc_offer_letter')->nullable()->after('doc_experience_letter');
            $table->string('doc_passport_photo')->nullable()->after('doc_offer_letter');

            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['designation_id']);

            $table->dropColumn([
                'employee_uid', 'phone', 'dob', 'gender', 'address',
                'branch_id', 'department_id', 'designation_id',
                'salary_type', 'basic_salary', 'joining_date', 'termination_date',
                'account_holder_name', 'account_number', 'bank_name', 'bank_branch',
                'ifsc_code', 'pan_number',
                'doc_aadhar_card', 'doc_pan_card', 'doc_marksheet_10th', 'doc_marksheet_12th',
                'doc_degree_certificate', 'doc_experience_letter', 'doc_offer_letter', 'doc_passport_photo',
            ]);
        });
    }
};
