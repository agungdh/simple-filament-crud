<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredBerkas;
use Database\Factories\WebinarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pegawai_id
 * @property string $materi_pengembangan
 * @property Carbon $tanggal_pelaksanaan
 * @property int $jumlah_jam
 * @property string|null $berkas
 * @property string|null $filename
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Pegawai $pegawai
 */
#[Fillable(['pegawai_id', 'materi_pengembangan', 'tanggal_pelaksanaan', 'jumlah_jam', 'berkas', 'filename'])]
class Webinar extends Model
{
    /** @use HasFactory<WebinarFactory> */
    use DeletesStoredBerkas, HasFactory;

    /**
     * @return BelongsTo<Pegawai, $this>
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pelaksanaan' => 'date',
        ];
    }
}
