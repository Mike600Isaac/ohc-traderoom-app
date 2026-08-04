<?php

use App\Http\Controllers\Admin\GlossaryAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/glossary', [GlossaryAdminController::class, 'index'])->name('glossary.index');
    Route::post('/glossary', [GlossaryAdminController::class, 'store'])->name('glossary.store');
    Route::patch('/glossary/{term}', [GlossaryAdminController::class, 'update'])->name('glossary.update');
});
