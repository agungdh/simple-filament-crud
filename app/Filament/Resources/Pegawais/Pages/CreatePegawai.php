<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Models\Pegawai;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected static bool $canCreateAnother = false;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Pegawai {
            $user = User::query()->create([
                'username' => $data['nip'],
                'password' => $data['password'],
            ]);

            $user->assignRole($data['role']);

            unset($data['password'], $data['password_confirmation'], $data['role']);

            return Pegawai::query()->create([
                ...$data,
                'user_id' => $user->id,
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
