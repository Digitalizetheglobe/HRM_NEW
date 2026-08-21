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
        if (!Schema::hasColumn('leaves', 'company_approved')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->unsignedBigInteger('forwarded_to_director_id')->nullable()->after('status');
                $table->unsignedBigInteger('forwarded_by_company_id')->nullable()->after('forwarded_to_director_id');
                $table->timestamp('forwarded_at')->nullable()->after('forwarded_by_company_id');
                $table->boolean('company_approved')->default(false)->after('forwarded_at');
                $table->boolean('director_approved')->default(false)->after('company_approved');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn([
                'forwarded_to_director_id',
                'forwarded_by_company_id',
                'forwarded_at',
                'company_approved',
                'director_approved'
            ]);
        });
    }
};
