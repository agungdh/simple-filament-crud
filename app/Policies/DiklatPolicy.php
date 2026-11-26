<?php

namespace App\Policies;

use App\Models\Diklat;
use App\Models\User;

class DiklatPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Diklat $diklat): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Diklat $diklat): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Diklat $diklat): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
