<?php

namespace App\Models;

use Database\Factories\PangkatGolonganFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $jenjang
 * @property string $pangkat
 * @property string $golongan
 * @property string $ruang
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $label
 */
#[Fillable(['jenjang', 'pangkat', 'golongan', 'ruang'])]
class PangkatGolongan extends Model
{
    /** @use HasFactory<PangkatGolonganFactory> */
    use HasFactory;

    /**
     * @return HasMany<Pegawai, $this>
     */
    public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }

    protected function label(): Attribute
    {
        return Attribute::get(
            fn (): string => sprintf('%s (%s/%s)', $this->pangkat, $this->golongan, $this->ruang),
        );
    }
}
