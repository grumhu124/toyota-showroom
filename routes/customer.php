<?php

use App\Http\Controllers\Inquiry\InquiryController;
use Illuminate\Support\Facades\Route;

Route::get('/yeu-cau', [InquiryController::class, 'index'])->name('inquiries.index');
Route::get('/yeu-cau/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
