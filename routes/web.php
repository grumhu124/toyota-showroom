<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/dang-ky', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/dang-ky', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/dang-nhap', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/dang-nhap', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'ensure.active'])->group(function (): void {
    Route::view('/tai-khoan', 'auth.account')->name('account');
    Route::post('/dang-xuat', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
