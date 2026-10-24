<?php

use App\Http\Controllers\Inquiry\InquiryController;
use Illuminate\Support\Facades\Route;

Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
foreach (['start', 'note', 'close'] as $action) {
    Route::patch('/inquiries/{inquiry}/'.$action, [InquiryController::class, 'update'])->name('inquiries.'.$action);
}
