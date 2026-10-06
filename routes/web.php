<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Admin\AppointmentConfirmationController;
use App\Http\Controllers\Admin\PharmacyOrderConfirmationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PharmacyOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route(auth()->check() ? 'home' : 'login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [AppointmentController::class, 'index'])->name('home');
    Route::get('/akun', [RegisteredUserController::class, 'show'])->name('account.show');

    Route::middleware('can:access-patient-services')->group(function (): void {
        Route::get('/janji-temu', [AppointmentController::class, 'createAppointment'])->name('appointments.create');
        Route::get('/antrean', [AppointmentController::class, 'createQueue'])->name('queue.create');
        Route::get('/konsultasi', [AppointmentController::class, 'createConsultation'])->name('consultations.create');
        Route::get('/bpjs', [AppointmentController::class, 'createBpjs'])->name('bpjs.create');
        Route::post('/janji-temu', [AppointmentController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('appointments.store');
        Route::post('/farmasi/keranjang', [PharmacyOrderController::class, 'checkout'])
            ->middleware('throttle:20,1')
            ->name('pharmacy.checkout');
        Route::get('/farmasi/pesanan', [PharmacyOrderController::class, 'create'])->name('pharmacy-orders.create');
        Route::post('/farmasi/pesanan', [PharmacyOrderController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('pharmacy-orders.store');
        Route::get('/farmasi', [PharmacyOrderController::class, 'index'])->name('pharmacy.index');
    });

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::middleware('can:manage-appointments')->group(function (): void {
            Route::get('/kunjungan', [AppointmentConfirmationController::class, 'index'])
                ->name('appointments.index');
            Route::post('/kunjungan/{appointment}/konfirmasi', [AppointmentConfirmationController::class, 'confirm'])
                ->name('appointments.confirm');
        });

        Route::middleware('can:manage-pharmacy-orders')->group(function (): void {
            Route::get('/pesanan-farmasi', [PharmacyOrderConfirmationController::class, 'index'])
                ->name('pharmacy-orders.index');
            Route::post('/pesanan-farmasi/{pharmacyOrder}/konfirmasi', [PharmacyOrderConfirmationController::class, 'confirm'])
                ->name('pharmacy-orders.confirm');
        });
    });
});
