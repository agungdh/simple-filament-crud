<?php

namespace App\Policies;

use App\Models\PangkatGolongan;
use App\Models\User;

class PangkatGolonganPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, PangkatGolongan $pangkatGolongan): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, PangkatGolongan $pangkatGolongan): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, PangkatGolongan $pangkatGolongan): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
