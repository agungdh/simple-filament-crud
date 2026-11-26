<?php

namespace App\Policies;

use App\Models\JenisPelatihan;
use App\Models\User;

class JenisPelatihanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, JenisPelatihan $jenisPelatihan): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, JenisPelatihan $jenisPelatihan): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, JenisPelatihan $jenisPelatihan): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
