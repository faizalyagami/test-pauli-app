<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PauliTestController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================================
// Public Routes
// ========================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ========================================
// Authentication Routes
// ========================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes (Optional)
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

// ========================================
// Profile Routes (For All Authenticated Users)
// ========================================
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/show', [ProfileController::class, 'show'])->name('show');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
    Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('upload-avatar');
    Route::delete('/avatar', [ProfileController::class, 'removeAvatar'])->name('remove-avatar');
    Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
});

// ========================================
// Admin Routes (Full Access)
// ========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    Route::post('/logs/clear', [AdminController::class, 'clearLogs'])->name('logs.clear');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
});

// ========================================
// Tester Routes (Operator/Psikolog)
// ========================================
Route::middleware(['auth', 'role:admin,tester'])->prefix('tester')->name('tester.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PauliTestController::class, 'dashboard'])->name('dashboard');

    // Test Management
    Route::get('/tests', [PauliTestController::class, 'manageTests'])->name('tests');
    Route::get('/tests/create', [PauliTestController::class, 'createTest'])->name('tests.create');
    Route::post('/tests', [PauliTestController::class, 'storeTest'])->name('tests.store');
    Route::get('/tests/{test}/edit', [PauliTestController::class, 'editTest'])->name('tests.edit');
    Route::put('/tests/{test}', [PauliTestController::class, 'updateTest'])->name('tests.update');
    Route::delete('/tests/{test}', [PauliTestController::class, 'destroyTest'])->name('tests.destroy');
    Route::post('/tester/tests/{test}/generate-questions', [PauliTestController::class, 'generateQuestions'])->name('tester.tests.generate');
    Route::put('/questions/{question}', [PauliTestController::class, 'updateQuestion'])->name('questions.update');

    // Applicant Management
    Route::get('/applicants', [PauliTestController::class, 'manageApplicants'])->name('applicants');
    Route::get('/applicants/create', [PauliTestController::class, 'createApplicant'])->name('applicants.create');
    Route::post('/applicants', [PauliTestController::class, 'storeApplicant'])->name('applicants.store');
    Route::get('/applicants/{applicant}', [PauliTestController::class, 'showApplicant'])->name('applicants.show');
    Route::get('/applicants/{applicant}/edit', [PauliTestController::class, 'editApplicant'])->name('applicants.edit');
    Route::put('/applicants/{applicant}', [PauliTestController::class, 'updateApplicant'])->name('applicants.update');
    Route::delete('/applicants/{applicant}', [PauliTestController::class, 'destroyApplicant'])->name('applicants.destroy');
    Route::post('/applicants/import', [PauliTestController::class, 'importApplicants'])->name('applicants.import');
    Route::get('/applicants/export', [PauliTestController::class, 'exportApplicants'])->name('applicants.export');

    // Session Management
    Route::get('/sessions', [PauliTestController::class, 'sessions'])->name('sessions');
    Route::get('/sessions/{session}', [PauliTestController::class, 'showSession'])->name('sessions.show');
    Route::get('/sessions/{session}/monitor', [PauliTestController::class, 'monitorSession'])->name('sessions.monitor');
    Route::post('/sessions/{session}/cancel', [PauliTestController::class, 'cancelSession'])->name('sessions.cancel');
    Route::get('/sessions/active', [PauliTestController::class, 'activeSessions'])->name('sessions.active');
    Route::get('/sessions/completed', [PauliTestController::class, 'completedSessions'])->name('sessions.completed');

    // Reports
    Route::get('/reports', [PauliTestController::class, 'reports'])->name('reports');
    Route::get('/reports/export-excel', [PauliTestController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/export-pdf', [PauliTestController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::post('/reports/export-results', [PauliTestController::class, 'exportResults'])->name('reports.export-results');

    // Profile
    Route::get('/profile', [ProfileController::class, 'testerProfile'])->name('profile');

    // ========================================
    // Settings Routes - PERBAIKAN (HAPUS DUPLIKASI)
    // ========================================
    Route::prefix('settings')->name('settings.')->group(function () {
        // Main settings page
        Route::get('/', [PauliTestController::class, 'testerSettings'])->name('index');
        Route::put('/', [PauliTestController::class, 'updateTesterSettings'])->name('update');

        // Logs
        Route::get('/logs', [PauliTestController::class, 'settingsLogs'])->name('logs');
        Route::post('/clear-logs', [PauliTestController::class, 'clearLogs'])->name('clear-logs');

        // Backup & Cache
        Route::post('/backup', [PauliTestController::class, 'createBackup'])->name('backup');
        Route::post('/clear-cache', [PauliTestController::class, 'clearCache'])->name('clear-cache');

        // User Management
        Route::get('/users', [PauliTestController::class, 'settingsUsers'])->name('users');
        Route::post('/users', [PauliTestController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [PauliTestController::class, 'getUser'])->name('users.show');
        Route::put('/users/{user}', [PauliTestController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [PauliTestController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [PauliTestController::class, 'toggleUserStatus'])->name('users.toggle-status');
    });

    // HAPUS DUPLIKASI INI - Jangan tambahkan route settings lagi di sini
    // Route::get('/settings', function () { ... })->name('settings');
    // Route::get('/settings', [PauliTestController::class, 'testerSettings'])->name('settings');
});

// ========================================
// Pauli Test Routes (Core Test Taking)
// ========================================
Route::middleware(['auth'])->prefix('pauli-test')->name('pauli-test.')->group(function () {
    Route::get('/{applicant}/{test}/start', [PauliTestController::class, 'startTest'])->name('start');
    Route::post('/save-answer', [PauliTestController::class, 'saveAnswer'])->name('save-answer');
    Route::post('/batch-answers', [PauliTestController::class, 'batchSaveAnswers'])->name('batch-answers');
    Route::post('/mark-line', [PauliTestController::class, 'markLine'])->name('mark-line');
    Route::post('/skip-column', [PauliTestController::class, 'skipColumn'])->name('skip-column');
    Route::post('/end', [PauliTestController::class, 'endTest'])->name('end');
    Route::get('/result/{session}', [PauliTestController::class, 'result'])->name('result');
    Route::get('/result/{session}/print', [PauliTestController::class, 'printResult'])->name('result.print');
    Route::get('/result/{session}/pdf', [PauliTestController::class, 'downloadPdf'])->name('result.pdf');
});

// ========================================
// Applicant Routes (For Test Participants)
// ========================================
Route::middleware(['auth', 'role:applicant'])->prefix('applicant')->name('applicant.')->group(function () {
    Route::get('/dashboard', [ApplicantController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ApplicantController::class, 'profile'])->name('profile');
    Route::put('/profile', [ApplicantController::class, 'updateProfile'])->name('profile.update');
    Route::get('/history', [ApplicantController::class, 'history'])->name('history');
    Route::get('/history/{session}', [ApplicantController::class, 'showResult'])->name('history.show');
    Route::get('/tests', [ApplicantController::class, 'availableTests'])->name('tests');
    Route::get('/tests/{test}/start', [ApplicantController::class, 'startTest'])->name('tests.start');
    Route::get('/statistics', [ApplicantController::class, 'statistics'])->name('statistics');
});

// ========================================
// Fallback Route (404 Not Found)
// ========================================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
