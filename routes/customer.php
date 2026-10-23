<?php

use Illuminate\Support\Facades\Route;

// Presentation only; the shared chat HTTP endpoints remain TV4's routes/inquiries.php.
// This file already has web/auth/active/role:customer middleware from bootstrap/app.php.
if (app()->environment(['local', 'testing'])) {
    Route::get('/tai-khoan/tin-nhan', function () {
        return view('customer.chat', [
            'conversations' => require resource_path('data/customer-chat-preview.php'),
        ]);
    })->name('chat.preview');
}
