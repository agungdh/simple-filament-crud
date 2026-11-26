<?php

namespace App\Filament\Resources\Concerns;

trait AssignsOwnedPegawai
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->hasRole('admin')) {
            $data['pegawai_id'] = auth()->user()?->pegawai?->id;
        }

        return $data;
    }
}
