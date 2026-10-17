<?php

use Illuminate\Support\Facades\Route;

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
