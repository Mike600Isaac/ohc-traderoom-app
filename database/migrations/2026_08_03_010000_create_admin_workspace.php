<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('member')->after('status')->index();
            }
            if (! Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('Africa/Lagos')->after('role');
            }
        });

        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('subject_type');
            $table->string('subject_id')->nullable();
            $table->string('summary');
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['actor_user_id', 'created_at']);
        });

        Schema::table('daily_game_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('daily_game_plans', 'status')) {
                $table->string('status')->default('draft')->after('title')->index();
            }
            if (! Schema::hasColumn('daily_game_plans', 'scheduled_for')) {
                $table->timestamp('scheduled_for')->nullable()->after('status')->index();
            }
            if (! Schema::hasColumn('daily_game_plans', 'author_user_id')) {
                $table->foreignId('author_user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('live_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('live_sessions', 'scheduled_for')) {
                $table->timestamp('scheduled_for')->nullable()->after('status')->index();
            }
            if (! Schema::hasColumn('live_sessions', 'host_user_id')) {
                $table->foreignId('host_user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::create('market_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body');
            $table->string('status')->default('draft')->index();
            $table->timestamp('scheduled_for')->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('learning_courses', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_free')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('scheduled_for')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('learning_courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('learning_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->string('title');
            $table->longText('body')->nullable();
            $table->string('video_url')->nullable();
            $table->string('document_path')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->boolean('is_preview')->default(false);
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('learning_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('learning_lessons')->cascadeOnDelete();
            $table->string('question');
            $table->json('options');
            $table->unsignedInteger('correct_option');
            $table->text('explanation')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('community_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('required_path')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_name')->index();
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'event_name']);
        });

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('platform')->index();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('community_channels');
        Schema::dropIfExists('learning_assessments');
        Schema::dropIfExists('learning_lessons');
        Schema::dropIfExists('learning_modules');
        Schema::dropIfExists('learning_courses');
        Schema::dropIfExists('market_reports');

        Schema::table('live_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('host_user_id');
            $table->dropColumn('scheduled_for');
        });

        Schema::table('daily_game_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_user_id');
            $table->dropColumn(['status', 'scheduled_for']);
        });

        Schema::dropIfExists('admin_audit_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'timezone']);
        });
    }
};
