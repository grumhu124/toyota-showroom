<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

// Own appointments: list + reschedule + cancel.
Route::get('/lich-hen', [AppointmentController::class, 'index'])->name('appointments.index');
Route::post('/lich-hen/{appointment}/doi', [AppointmentController::class, 'reschedule'])
    ->name('appointments.reschedule');
Route::post('/lich-hen/{appointment}/huy', [AppointmentController::class, 'cancel'])
    ->name('appointments.cancel');

// UI preview only; shared chat HTTP remains separate.
if (app()->environment(['local', 'testing'])) {
    Route::get('/tai-khoan/tin-nhan', function () {
        return view('customer.chat', [
            'conversations' => require resource_path('data/customer-chat-preview.php'),
        ]);
    })->name('chat.preview');
}
