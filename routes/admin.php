<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CommunityChannelAdminController;
use App\Http\Controllers\Admin\GamePlanController;
use App\Http\Controllers\Admin\LearningAdminController;
use App\Http\Controllers\Admin\LiveSessionAdminController;
use App\Http\Controllers\Admin\MarketReportController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PlatformSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/analytics', AdminAnalyticsController::class)->name('analytics.index');

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::patch('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::post('/members/{member}/entitlements', [MemberController::class, 'storeEntitlement'])->name('members.entitlements.store');
    Route::patch('/members/{member}/entitlements/{entitlement}', [MemberController::class, 'updateEntitlement'])->name('members.entitlements.update');

    Route::get('/game-plans', [GamePlanController::class, 'index'])->name('game-plans.index');
    Route::get('/game-plans/create', [GamePlanController::class, 'create'])->name('game-plans.create');
    Route::post('/game-plans', [GamePlanController::class, 'store'])->name('game-plans.store');
    Route::get('/game-plans/{gamePlan}/edit', [GamePlanController::class, 'edit'])->name('game-plans.edit');
    Route::patch('/game-plans/{gamePlan}', [GamePlanController::class, 'update'])->name('game-plans.update');

    Route::get('/live-sessions', [LiveSessionAdminController::class, 'index'])->name('sessions.index');
    Route::get('/live-sessions/create', [LiveSessionAdminController::class, 'create'])->name('sessions.create');
    Route::post('/live-sessions', [LiveSessionAdminController::class, 'store'])->name('sessions.store');
    Route::get('/live-sessions/{session}/edit', [LiveSessionAdminController::class, 'edit'])->name('sessions.edit');
    Route::patch('/live-sessions/{session}', [LiveSessionAdminController::class, 'update'])->name('sessions.update');

    Route::get('/market-reports', [MarketReportController::class, 'index'])->name('reports.index');
    Route::get('/market-reports/create', [MarketReportController::class, 'create'])->name('reports.create');
    Route::post('/market-reports', [MarketReportController::class, 'store'])->name('reports.store');
    Route::get('/market-reports/{report}/edit', [MarketReportController::class, 'edit'])->name('reports.edit');
    Route::patch('/market-reports/{report}', [MarketReportController::class, 'update'])->name('reports.update');

    Route::get('/courses', [LearningAdminController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [LearningAdminController::class, 'create'])->name('courses.create');
    Route::post('/courses', [LearningAdminController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}/edit', [LearningAdminController::class, 'editCourse'])->name('courses.edit');
    Route::patch('/courses/{course}', [LearningAdminController::class, 'updateCourse'])->name('courses.update');
    Route::post('/courses/{course}/modules', [LearningAdminController::class, 'storeModule'])->name('courses.modules.store');
    Route::patch('/courses/{course}/modules/{module}', [LearningAdminController::class, 'updateModule'])->name('courses.modules.update');
    Route::get('/modules/{module}/lessons/create', [LearningAdminController::class, 'createLesson'])->name('modules.lessons.create');
    Route::post('/modules/{module}/lessons', [LearningAdminController::class, 'storeLesson'])->name('modules.lessons.store');
    Route::get('/lessons/{lesson}/edit', [LearningAdminController::class, 'editLesson'])->name('lessons.edit');
    Route::patch('/lessons/{lesson}', [LearningAdminController::class, 'updateLesson'])->name('lessons.update');
    Route::post('/lessons/{lesson}/assessments', [LearningAdminController::class, 'storeAssessment'])->name('lessons.assessments.store');

    Route::get('/community', [CommunityChannelAdminController::class, 'index'])->name('community.index');
    Route::post('/community', [CommunityChannelAdminController::class, 'store'])->name('community.store');
    Route::patch('/community/{channel}', [CommunityChannelAdminController::class, 'update'])->name('community.update');

    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/settings', [PlatformSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [PlatformSettingsController::class, 'update'])->name('settings.update');
});
