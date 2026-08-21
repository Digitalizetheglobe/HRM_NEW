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
        \DB::statement('ALTER TABLE design_feedbacks MODIFY submitted_by BIGINT UNSIGNED NULL');
        \DB::statement('ALTER TABLE design_feedback_attachments MODIFY uploaded_by BIGINT UNSIGNED NULL');
        \DB::statement('ALTER TABLE design_activity_logs MODIFY user_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement('ALTER TABLE design_feedbacks MODIFY submitted_by BIGINT UNSIGNED NOT NULL');
        \DB::statement('ALTER TABLE design_feedback_attachments MODIFY uploaded_by BIGINT UNSIGNED NOT NULL');
        \DB::statement('ALTER TABLE design_activity_logs MODIFY user_id BIGINT UNSIGNED NOT NULL');
    }
};
