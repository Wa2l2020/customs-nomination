<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttachmentController;

// Public Routes
Route::get('/', function () {
    $settings = \App\Models\Setting::pluck('value', 'key');
    return view('welcome', compact('settings'));
});
Route::get('/nomination', [SystemController::class, 'showNomination'])->name('nomination');
Route::post('/nomination', [SystemController::class, 'submitNomination']);
Route::post('/nomination/status-inquiry', [SystemController::class, 'checkStatus'])->name('nomination.inquiry');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::any('/logout', [AuthController::class, 'logout'])->name('logout'); // Changed to any to support GET
Route::get('/users/search', [AuthController::class, 'searchUsers'])->name('users.search');

// Registration (The Builder)
Route::get('/register', [SystemController::class, 'showRegister'])->name('register');
Route::post('/register', [SystemController::class, 'register']);


// Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [SystemController::class, 'dashboard'])->name('dashboard');
    Route::post('/nomination/{id}/status', [SystemController::class, 'updateStatus'])->name('nomination.status');
    Route::get('/nomination/{id}/view', [SystemController::class, 'show'])->name('nomination.show');

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings');
        Route::post('/reset', [AdminController::class, 'resetSystem'])->name('admin.reset');
        Route::post('/fake-data', [AdminController::class, 'seedFakeData'])->name('admin.fake');
        
        // New Pages
        Route::get('/nominations', [AdminController::class, 'nominations'])->name('admin.nominations');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');
        Route::get('/export', [AdminController::class, 'export'])->name('admin.export');
        Route::post('/export', [AdminController::class, 'exportSystem'])->name('admin.export.system');
        Route::get('/export/attachments', [AdminController::class, 'downloadAttachmentsArchive'])->name('admin.export.attachments');
        Route::post('/restore', [AdminController::class, 'restore'])->name('admin.restore');
        Route::post('/seed', [AdminController::class, 'seedDatabase'])->name('admin.seed');
        Route::post('/reset', [AdminController::class, 'resetDatabase'])->name('admin.reset');
        Route::post('/sync', [AdminController::class, 'syncFiles'])->name('admin.sync');
        Route::get('/mass-email', [AdminController::class, 'showMassEmailForm'])->name('admin.mass_email');
        Route::post('/mass-email', [AdminController::class, 'sendMassEmail'])->name('admin.mass_email.send');
        Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.update');
        Route::post('/users/{id}/update-details', [AdminController::class, 'updateUser'])->name('admin.users.update_details');
        Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::post('/users/{id}/update-password', [AdminController::class, 'updateUserPassword'])->name('admin.users.update_password');
        
        // Monitor
        // Attachments Management
        Route::get('/attachments', [AttachmentController::class, 'index'])->name('admin.attachments.index');
        Route::post('/attachments/import', [AttachmentController::class, 'importLinks'])->name('admin.attachments.import');
        Route::post('/attachments/archive', [AttachmentController::class, 'archive'])->name('admin.attachments.archive');
        Route::post('/attachments/{id}/update-link', [AttachmentController::class, 'updateLink'])->name('admin.attachments.update_link');
        Route::get('/attachments/view/{folder}/{filename}', [AttachmentController::class, 'viewFile'])->name('admin.attachments.view');

        Route::get('/monitor', [App\Http\Controllers\AdminController::class, 'monitor'])->name('admin.monitor');

        // New Routes
        Route::post('/nomination/{id}/evaluate', [AdminController::class, 'submitEvaluation'])->name('admin.evaluate');
        Route::post('/nomination/{id}/approve', [AdminController::class, 'approveNomination'])->name('admin.approve');
        Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
        Route::post('/settings/manual-email', [AdminController::class, 'manualEmailAdd'])->name('admin.settings.manual_email');
        
        // Workflow Routes
        Route::post('/workflow/general-to-central', [AdminController::class, 'promoteGeneralToCentral'])->name('admin.workflow.general_to_central');
        Route::post('/workflow/central-to-committee', [AdminController::class, 'promoteCentralToCommittee'])->name('admin.workflow.central_to_committee');
        Route::post('/nomination/{id}/restore', [AdminController::class, 'restoreNomination'])->name('admin.nomination.restore');
        
        // Category Management
        Route::resource('categories', \App\Http\Controllers\CategoryController::class, ['as' => 'admin']);
        Route::post('categories/{category}/questions', [\App\Http\Controllers\CategoryController::class, 'storeQuestion'])->name('admin.categories.questions.store');
        Route::delete('questions/{question}', [\App\Http\Controllers\CategoryController::class, 'destroyQuestion'])->name('admin.questions.destroy');
    });
});
