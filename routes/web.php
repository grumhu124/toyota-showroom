<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/inquiries.php';

Route::get('/', function () {
    return app()->environment(['local', 'testing']) ? view('home') : view('welcome');
})->name('home');

// Frontend-only preview. Catalog owner will provide the production data route.
if (app()->environment(['local', 'testing'])) {
    Route::get('/xe', function () {
        return view('cars.index', require resource_path('data/collection-preview.php'));
    })->name('cars.preview');

    Route::get('/xe/{id}', function (int $id) {
        $data = require resource_path('data/car-details-preview.php');
        abort_unless(isset($data[$id]), 404);

        return view('cars.show', [
            'car' => $data[$id],
            'related' => array_slice(array_values(array_filter($data, fn (array $car): bool => $car['id'] !== $id)), 0, 2),
        ]);
    })->whereNumber('id')->name('cars.preview.show');
}

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
