<?php

namespace App\Policies;

use App\Models\Ppm;
use App\Models\User;

class PpmPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Ppm $ppm): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Ppm $ppm): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Ppm $ppm): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
