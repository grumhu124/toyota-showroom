<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// TV4 replaces this deny-by-default callback with version + record authorization.
Broadcast::channel('inquiry.{inquiryId}.v{accessVersion}', function (User $user, string $inquiryId, string $accessVersion): bool {
    return false;
});
