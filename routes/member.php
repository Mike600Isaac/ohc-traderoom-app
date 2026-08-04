<?php

use App\Http\Controllers\Member\CommunityController;
use App\Http\Controllers\Member\JournalController;
use App\Http\Controllers\Member\LearningWorkspaceController;
use App\Http\Controllers\Member\MarketWorkspaceController;
use App\Http\Controllers\Member\NotificationCenterController;
use App\Http\Controllers\Member\PortfolioController;
use App\Http\Controllers\Member\ResearchController;
use App\Http\Controllers\Member\TradeRoomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/markets', [MarketWorkspaceController::class, 'index'])->name('markets.index');

    Route::get('/learn', [LearningWorkspaceController::class, 'index'])->name('learn.index');
    Route::get('/learn/{course:key}', [LearningWorkspaceController::class, 'course'])->name('learn.course');
    Route::get('/learn/{course:key}/lessons/{lesson}', [LearningWorkspaceController::class, 'lesson'])->name('learn.lesson');
    Route::patch('/learn/lessons/{lesson}/progress', [LearningWorkspaceController::class, 'progress'])->name('learn.progress');

    Route::get('/trade-room', [TradeRoomController::class, 'index'])->name('trade-room.index');
    Route::post('/trade-room/{session}/reminder', [TradeRoomController::class, 'reminder'])->name('trade-room.reminder');

    Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');
    Route::delete('/journal/{trade}', [JournalController::class, 'destroy'])->name('journal.destroy');

    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio', [PortfolioController::class, 'storePortfolio'])->name('portfolio.store');
    Route::post('/portfolio/{portfolio}/holdings', [PortfolioController::class, 'storeHolding'])->name('portfolio.holdings.store');
    Route::delete('/portfolio/{portfolio}/holdings/{holding}', [PortfolioController::class, 'destroyHolding'])->name('portfolio.holdings.destroy');

    Route::get('/research', [ResearchController::class, 'index'])->name('research.index');
    Route::get('/research/reports/{report:slug}', [ResearchController::class, 'report'])->name('research.report');

    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::get('/community/{channel:slug}', [CommunityController::class, 'channel'])->name('community.channel');
    Route::post('/community/{channel:slug}', [CommunityController::class, 'store'])->name('community.store');

    Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}', [NotificationCenterController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationCenterController::class, 'readAll'])->name('notifications.read-all');
    Route::put('/notifications/preferences', [NotificationCenterController::class, 'preferences'])->name('notifications.preferences');
});
