<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredBerkas;
use Database\Factories\DiklatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pegawai_id
 * @property int $jenis_pelatihan_id
 * @property string $nomor_surat
 * @property string $materi_pengembangan
 * @property Carbon $dari_tanggal_pelaksanaan
 * @property Carbon $sampai_tanggal_pelaksanaan
 * @property int $jumlah_jam_pelatihan
 * @property string|null $berkas
 * @property string|null $filename
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Pegawai $pegawai
 * @property-read JenisPelatihan $jenisPelatihan
 */
#[Fillable([
    'pegawai_id',
    'jenis_pelatihan_id',
    'nomor_surat',
    'materi_pengembangan',
    'dari_tanggal_pelaksanaan',
    'sampai_tanggal_pelaksanaan',
    'jumlah_jam_pelatihan',
    'berkas',
    'filename',
])]
class Diklat extends Model
{
    /** @use HasFactory<DiklatFactory> */
    use DeletesStoredBerkas, HasFactory;

    /**
     * @return BelongsTo<Pegawai, $this>
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * @return BelongsTo<JenisPelatihan, $this>
     */
    public function jenisPelatihan(): BelongsTo
    {
        return $this->belongsTo(JenisPelatihan::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dari_tanggal_pelaksanaan' => 'date',
            'sampai_tanggal_pelaksanaan' => 'date',
        ];
    }
}
