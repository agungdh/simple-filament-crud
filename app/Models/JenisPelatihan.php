<?php

namespace App\Models;

use Database\Factories\JenisPelatihanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $jenis_pelatihan
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Diklat> $diklats
 */
#[Fillable(['jenis_pelatihan'])]
class JenisPelatihan extends Model
{
    /** @use HasFactory<JenisPelatihanFactory> */
    use HasFactory;

    /**
     * @return HasMany<Diklat, $this>
     */
    public function diklats(): HasMany
    {
        return $this->hasMany(Diklat::class);
    }
}
