<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

class StoreSalesRequest extends RegisterRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }
}
