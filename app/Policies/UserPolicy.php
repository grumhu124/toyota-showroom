<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->is_active && $actor->role === UserRole::Admin;
    }

    public function create(User $actor): bool
    {
        return $this->viewAny($actor);
    }

    public function updateStatus(User $actor, User $target): bool
    {
        return $this->viewAny($actor)
            && in_array($target->role, [UserRole::Customer, UserRole::Sales], true);
    }
}
