<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\User;

class PegawaiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Pegawai $pegawai): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Pegawai $pegawai): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Pegawai $pegawai): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
