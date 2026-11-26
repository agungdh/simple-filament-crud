<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait ManagesOwnedPegawaiRecord
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'pegawai']);
    }

    public function view(User $user, Model $record): bool
    {
        return $this->owns($user, $record);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->pegawai !== null;
    }

    public function update(User $user, Model $record): bool
    {
        return $this->owns($user, $record);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->owns($user, $record);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    private function owns(User $user, Model $record): bool
    {
        return $user->hasRole('admin')
            || ($user->pegawai !== null && $record->getAttribute('pegawai_id') === $user->pegawai->id);
    }
}
