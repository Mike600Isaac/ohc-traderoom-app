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
    ->middleware(['auth', 'verified'])
    ->name('subscribe.show');

Route::post('/subscribe/{course}/pay', [PaymentController::class, 'start'])
    ->middleware(['auth', 'verified'])
    ->name('subscribe.start');

Route::get('/payments/callback', [PaymentController::class, 'callback'])
    ->name('payments.callback');

// 2. The Member Dashboard (requires login)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
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
    ->middleware(['auth', 'verified'])
    ->name('courses.index');

// 4. Authentication Routes (Login, Logout, etc.)
// <public-content-routes>
Route::view('/about', 'pages.about')->name('about');

Route::prefix('programs')->name('programs.')->group(function () {
    Route::view('/markets-basics', 'pages.course-basics')->name('basics');
    Route::view('/equity-analysis', 'pages.course-equity')->name('equity');
    Route::view('/fixed-income', 'pages.course-fixed-income')->name('fixed-income');
    Route::view('/lmrss', 'pages.course-lmrss')->name('lmrss');
    Route::view('/derivatives', 'pages.course-derivatives')->name('derivatives');
});

Route::view('/terms', 'pages.terms')->name('legal.terms');
Route::view('/privacy', 'pages.privacy')->name('legal.privacy');
Route::view('/risk-disclaimer', 'pages.risk-disclaimer')->name('legal.risk-disclaimer');
Route::view('/cookie-policy', 'pages.cookie-policy')->name('legal.cookie-policy');
Route::view('/success', 'pages.success')->name('success');

Route::redirect('/index.html', '/', 301);
Route::redirect('/about.html', '/about', 301);
Route::redirect('/course-basics.html', '/programs/markets-basics', 301);
Route::redirect('/course-equity.html', '/programs/equity-analysis', 301);
Route::redirect('/course-fixed-income.html', '/programs/fixed-income', 301);
Route::redirect('/course-lmrss.html', '/programs/lmrss', 301);
Route::redirect('/course-derivatives.html', '/programs/derivatives', 301);
Route::redirect('/terms.html', '/terms', 301);
Route::redirect('/privacy.html', '/privacy', 301);
Route::redirect('/risk-disclaimer.html', '/risk-disclaimer', 301);
Route::redirect('/cookie-policy.html', '/cookie-policy', 301);
Route::redirect('/success.html', '/success', 301);
// </public-content-routes>

require __DIR__.'/auth.php';

require __DIR__.'/admin.php';
require __DIR__.'/member.php';
require __DIR__.'/member-admin.php';
