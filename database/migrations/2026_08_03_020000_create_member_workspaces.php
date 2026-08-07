<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('learning_lessons')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('bookmarked')->default(false);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });

        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('instrument', 32)->index();
            $table->string('direction', 8);
            $table->decimal('entry_price', 20, 8)->nullable();
            $table->decimal('stop_price', 20, 8)->nullable();
            $table->decimal('target_price', 20, 8)->nullable();
            $table->decimal('risk_percent', 5, 2)->nullable();
            $table->string('emotion', 32)->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->string('outcome', 16)->default('open')->index();
            $table->decimal('r_multiple', 8, 2)->nullable();
            $table->string('screenshot_path')->nullable();
            $table->text('lessons')->nullable();
            $table->timestamp('traded_at')->index();
            $table->timestamps();
            $table->index(['user_id', 'traded_at']);
        });

        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('benchmark_symbol', 24)->default('SPY');
            $table->string('currency', 3)->default('USD');
            $table->timestamps();
        });

        Schema::create('holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 32);
            $table->string('name')->nullable();
            $table->string('asset_class', 40);
            $table->decimal('quantity', 20, 8);
            $table->decimal('average_cost', 20, 8);
            $table->decimal('target_weight', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['portfolio_id', 'symbol']);
        });

        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('community_channels')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('community_posts')->cascadeOnDelete();
            $table->text('body');
            $table->string('attachment_url')->nullable();
            $table->timestamps();
            $table->index(['channel_id', 'created_at']);
        });

        Schema::create('session_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('live_session_id')->constrained('live_sessions')->cascadeOnDelete();
            $table->timestamp('remind_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'live_session_id']);
        });

        Schema::create('glossary_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term')->unique();
            $table->text('definition');
            $table->string('status')->default('draft')->index();
            $table->foreignId('author_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->boolean('in_app')->default(true);
            $table->boolean('email')->default(true);
            $table->boolean('push')->default(false);
            $table->time('quiet_start')->nullable();
            $table->time('quiet_end')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'type']);
        });

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('live_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('live_sessions', 'replay_url')) {
                $table->string('replay_url')->nullable()->after('join_url');
            }
            if (! Schema::hasColumn('live_sessions', 'recap')) {
                $table->text('recap')->nullable()->after('agenda');
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            $columns = array_filter(['replay_url', 'recap'], fn ($column) => Schema::hasColumn('live_sessions', $column));
            if ($columns) $table->dropColumn($columns);
        });
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('glossary_terms');
        Schema::dropIfExists('session_reminders');
        Schema::dropIfExists('community_posts');
        Schema::dropIfExists('holdings');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('trades');
        Schema::dropIfExists('learning_lesson_progress');
    }
};
