<?php

use App\Http\Controllers\AssessmentResultsController;
use App\Http\Controllers\AssessmentsController;
use App\Http\Controllers\AssignmentGradesController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CertificatesController;
use App\Http\Controllers\ContactMessagesController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentsController;
use App\Http\Controllers\GroupSchedulesController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\GuardiansController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SessionAttendanceController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubmissionsController;
use App\Http\Controllers\TeachersController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', [PagesController::class, 'home'])->name('home');
Route::get('/about', [PagesController::class, 'about'])->name('about');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::post('/contact', [PagesController::class, 'sendContact'])->middleware('throttle:5,1')->name('contact.send');
Route::get('/catalog', [PagesController::class, 'courses'])->name('catalog');
Route::get('/catalog/{course}', [PagesController::class, 'courseDetails'])->name('catalog.show');

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/sessions', [SessionsController::class, 'index'])->name('sessions.index');

    Route::middleware('role:super_admin,manager')->group(function (): void {
        Route::resource('parents', GuardiansController::class)->parameters(['parents' => 'guardian']);
        Route::resource('students', StudentsController::class);
        Route::resource('contact-messages', ContactMessagesController::class)->only(['index', 'show', 'destroy']);
        Route::resource('courses', CoursesController::class);
        Route::resource('teachers', TeachersController::class);
        Route::resource('groups', GroupsController::class);
        Route::resource('enrollments', EnrollmentsController::class)->except(['destroy']);
        Route::get('/reports/attendance', [ReportsController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/progress', [ReportsController::class, 'progress'])->name('reports.progress');
        Route::get('/reports/courses', [ReportsController::class, 'courses'])->name('reports.courses');
        Route::get('/audit-logs', [AuditLogsController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogsController::class, 'show'])->name('audit-logs.show');
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
    Route::get('/sessions/{session}/attendance', [SessionAttendanceController::class, 'edit'])->name('sessions.attendance.edit');
    Route::put('/sessions/{session}/attendance', [SessionAttendanceController::class, 'update'])->name('sessions.attendance.update');

    Route::get('/certificates/{certificate}', [CertificatesController::class, 'show'])->name('certificates.show');
    Route::post('/enrollments/{enrollment}/certificates', [CertificatesController::class, 'store'])->name('enrollments.certificates.store');

    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationsController::class, 'readAll'])->name('notifications.read-all');

    Route::resource('groups.assessments', AssessmentsController::class)->scoped();
    Route::get('/groups/{group}/assessments/{assessment}/results', [AssessmentResultsController::class, 'edit'])->name('groups.assessments.results.edit')->scopeBindings();
    Route::put('/groups/{group}/assessments/{assessment}/results', [AssessmentResultsController::class, 'update'])->name('groups.assessments.results.update')->scopeBindings();

    Route::resource('groups.assignments', AssignmentsController::class)->scoped();
    Route::resource('groups.assignments.submissions', SubmissionsController::class)->only(['store', 'update'])->scoped();
    Route::get('/groups/{group}/assignments/{assignment}/grades', [AssignmentGradesController::class, 'edit'])->name('groups.assignments.grades.edit')->scopeBindings();
    Route::put('/groups/{group}/assignments/{assignment}/grades', [AssignmentGradesController::class, 'update'])->name('groups.assignments.grades.update')->scopeBindings();

    Route::middleware('role:super_admin,manager,accountant')->group(function (): void {
        Route::resource('payments', PaymentsController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('/payments/{payment}/refund', [PaymentsController::class, 'refund'])->name('payments.refund');
        Route::get('/reports/financial', [ReportsController::class, 'financial'])->name('reports.financial');
    });
});
