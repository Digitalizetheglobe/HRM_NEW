<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chat_groups')) {
            Schema::create('chat_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('avatar')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chat_group_members')) {
            Schema::create('chat_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->cascadeOnDelete();
                $table->unsignedBigInteger('user_id');
                $table->string('role', 20)->default('member'); // admin | member
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();

                $table->unique(['group_id', 'user_id']);
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('chat_group_messages')) {
            Schema::create('chat_group_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->cascadeOnDelete();
                $table->unsignedBigInteger('sender_id');
                $table->text('body')->nullable();
                $table->string('attachment')->nullable();
                $table->string('attachment_name')->nullable();
                $table->timestamps();

                $table->index(['group_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_group_messages');
        Schema::dropIfExists('chat_group_members');
        Schema::dropIfExists('chat_groups');
    }
};

