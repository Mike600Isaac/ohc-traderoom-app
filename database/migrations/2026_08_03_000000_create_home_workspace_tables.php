<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course_key');
            $table->string('current_item_title')->nullable();
            $table->unsignedInteger('completed_items')->default(0);
            $table->unsignedInteger('total_items')->nullable();
            $table->string('resume_url')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_key']);
        });

        Schema::create('weekly_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_starts_on');
            $table->unsignedInteger('target');
            $table->unsignedInteger('completed')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'week_starts_on']);
        });

        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('agenda')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('join_url')->nullable();
            $table->string('status')->default('scheduled');
            $table->unsignedInteger('registered_count')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['published_at', 'starts_at']);
        });

        Schema::create('daily_game_plans', function (Blueprint $table) {
            $table->id();
            $table->date('trading_date');
            $table->string('title');
            $table->string('market')->nullable();
            $table->string('bias')->nullable();
            $table->json('key_levels')->nullable();
            $table->text('invalidation')->nullable();
            $table->json('watchlist')->nullable();
            $table->string('video_url')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('chart_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['trading_date', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_game_plans');
        Schema::dropIfExists('live_sessions');
        Schema::dropIfExists('weekly_goals');
        Schema::dropIfExists('course_progress');
    }
};