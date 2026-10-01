<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\EnrollmentsController;
use App\Http\Controllers\GroupSchedulesController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\GuardiansController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
        session()->put('locale', $locale);
    }

    return back();
})->name('locale.switch');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/sessions', [SessionsController::class, 'index'])->name('sessions.index');

    Route::middleware('role:super_admin,manager')->group(function (): void {
        Route::resource('parents', GuardiansController::class)->parameters(['parents' => 'guardian']);
        Route::resource('students', StudentsController::class);
        Route::resource('courses', CoursesController::class);
        Route::resource('teachers', TeachersController::class);
        Route::resource('groups', GroupsController::class);
        Route::resource('enrollments', EnrollmentsController::class)->except(['destroy']);
        Route::resource('groups.schedules', GroupSchedulesController::class)->except(['create', 'show'])->scoped();

        Route::get('/sessions/create', [SessionsController::class, 'create'])->name('sessions.create');
        Route::post('/sessions', [SessionsController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{session}/edit', [SessionsController::class, 'edit'])->name('sessions.edit');
        Route::put('/sessions/{session}', [SessionsController::class, 'update'])->name('sessions.update');
        Route::delete('/sessions/{session}', [SessionsController::class, 'destroy'])->name('sessions.destroy');

        Route::get('/groups/{group}/sessions/generate', [SessionsController::class, 'generate'])->name('sessions.generate');
        Route::post('/groups/{group}/sessions/generate', [SessionsController::class, 'storeGenerated'])->name('sessions.generate.store');
    });

    Route::get('/sessions/{session}', [SessionsController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{session}/status', [SessionsController::class, 'updateStatus'])->name('sessions.status');
});
