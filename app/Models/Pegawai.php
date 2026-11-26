<?php

namespace App\Models;

use App\Enums\KategoriJabatan;
use App\Enums\KategoriKebutuhanJamPelatihan;
use App\Enums\StatusPegawai;
use Database\Factories\PegawaiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $nip
 * @property string $nama
 * @property int $bidang_id
 * @property int|null $pangkat_golongan_id
 * @property StatusPegawai $status
 * @property string|null $jabatan
 * @property KategoriJabatan|null $kategori_jabatan
 * @property string|null $peran
 * @property KategoriKebutuhanJamPelatihan|null $kategori_kebutuhan_jam_pelatihan
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Bidang $bidang
 * @property-read PangkatGolongan|null $pangkatGolongan
 */
#[Fillable([
    'user_id',
    'nip',
    'nama',
    'bidang_id',
    'pangkat_golongan_id',
    'status',
    'jabatan',
    'kategori_jabatan',
    'peran',
    'kategori_kebutuhan_jam_pelatihan',
])]
class Pegawai extends Model
{
    /** @use HasFactory<PegawaiFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Bidang, $this>
     */
    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    /**
     * @return BelongsTo<PangkatGolongan, $this>
     */
    public function pangkatGolongan(): BelongsTo
    {
        return $this->belongsTo(PangkatGolongan::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusPegawai::class,
            'kategori_jabatan' => KategoriJabatan::class,
            'kategori_kebutuhan_jam_pelatihan' => KategoriKebutuhanJamPelatihan::class,
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (Pegawai $pegawai): void {
            $pegawai->user?->delete();
        });
    }
}
