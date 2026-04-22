<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\IncidentCategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Satgas\DashboardController as SatgasDashboardController;
use App\Http\Controllers\Satgas\IncidentReviewController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\IncidentReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        /** @var \App\Models\User $user */
        $user = auth()->user()->loadMissing('role');

        return redirect()->route($user->dashboardRouteName());
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'active.user', 'role:mahasiswa'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [IncidentReportController::class, 'index'])->name('index');
        Route::get('/create', [IncidentReportController::class, 'create'])->name('create');
        Route::post('/', [IncidentReportController::class, 'store'])->name('store');
        Route::get('/{incidentReport}', [IncidentReportController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'active.user', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('locations', LocationController::class)->except(['show', 'destroy']);
    Route::resource('incident-categories', IncidentCategoryController::class)->except(['show', 'destroy']);
});

Route::middleware(['auth', 'active.user', 'role:satgas'])->prefix('satgas')->name('satgas.')->group(function () {
    Route::get('/dashboard', SatgasDashboardController::class)->name('dashboard');

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [IncidentReviewController::class, 'index'])->name('index');
        Route::get('/{incidentReport}', [IncidentReviewController::class, 'show'])->name('show');
        Route::patch('/{incidentReport}/verify', [IncidentReviewController::class, 'verify'])->name('verify');
    });
});
