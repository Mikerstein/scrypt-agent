<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\EngagementController;
use App\Http\Controllers\AccountController;

// Public — redirect root to login if not authenticated
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// All protected routes
Route::middleware(['auth'])->group(function () {

    Route::prefix('account')->name('account.')->group(function () {
    Route::get('/',                [AccountController::class, 'show'])->name('show');
    Route::patch('/profile',       [AccountController::class, 'updateProfile'])->name('update-profile');
    Route::patch('/password',      [AccountController::class, 'updatePassword'])->name('update-password');
});

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Content
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/',                         [ContentController::class, 'index'])->name('index');
        Route::post('/generate',                [ContentController::class, 'generate'])->name('generate');
        Route::get('/{contentItem}',            [ContentController::class, 'show'])->name('show');
        Route::patch('/{contentItem}/status',   [ContentController::class, 'updateStatus'])->name('status');
        Route::post('/{contentItem}/schedule',  [ContentController::class, 'schedule'])->name('schedule');
    });

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    // Leads
    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('/',                         [LeadController::class, 'index'])->name('index');
        Route::post('/',                        [LeadController::class, 'store'])->name('store');
        Route::get('/{lead}',                   [LeadController::class, 'show'])->name('show');
        Route::patch('/{lead}/status',          [LeadController::class, 'updateStatus'])->name('status');
        Route::post('/{lead}/activity',         [LeadController::class, 'addActivity'])->name('activity');
    });

    // KPI
    Route::get('/kpi', [KpiController::class, 'index'])->name('kpi');

    // Engagement
    Route::prefix('engagement')->name('engagement.')->group(function () {
        Route::get('/',                             [EngagementController::class, 'index'])->name('index');
        Route::patch('/{engagement}/approve',       [EngagementController::class, 'approve'])->name('approve');
        Route::patch('/{engagement}/reject',        [EngagementController::class, 'reject'])->name('reject');
        Route::post('/{engagement}/regenerate',     [EngagementController::class, 'regenerate'])->name('regenerate');
    });

});

// Breeze auth routes (login, register, password reset)
require __DIR__.'/auth.php';