<?php

namespace App\Policies;

use App\Models\MateriPpm;
use App\Models\User;

class MateriPpmPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, MateriPpm $materiPpm): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, MateriPpm $materiPpm): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, MateriPpm $materiPpm): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
