<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Models\Pegawai;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    /**
     * @param  Pegawai  $record
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $user = $record->user;

            $user->username = $data['nip'];

            if (filled($data['password'] ?? null)) {
                $user->password = $data['password'];
            }

            $user->save();

            $user->syncRoles([$data['role']]);

            unset($data['password'], $data['password_confirmation'], $data['role']);

            $record->update($data);

            return $record;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
