<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// 1. The Public Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/subscribe/{course}', [PaymentController::class, 'show'])
    ->middleware(['auth'])
    ->name('subscribe.show');

Route::post('/subscribe/{course}/pay', [PaymentController::class, 'start'])
    ->middleware(['auth'])
    ->name('subscribe.start');

Route::get('/payments/callback', [PaymentController::class, 'callback'])
    ->name('payments.callback');

// 2. The Member Dashboard (requires login)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// GET logout bridge for the marketing pages' logout links
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});

// 3. Profile Management (Standard Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//courses routes
Route::get('/courses', [CourseController::class, 'index'])
    ->middleware(['auth'])
    ->name('courses.index');

// 4. Authentication Routes (Login, Logout, etc.)
require __DIR__.'/auth.php';
