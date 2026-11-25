<?php

namespace App\Models;

use Database\Factories\BidangFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $bidang
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Pegawai> $pegawais
 */
#[Fillable(['bidang'])]
class Bidang extends Model
{
    /** @use HasFactory<BidangFactory> */
    use HasFactory;

    /**
     * @return HasMany<Pegawai, $this>
     */
    public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }
}
