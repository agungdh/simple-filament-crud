<?php

namespace App\Models;

use Database\Factories\MateriPpmFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $nomor_surat
 * @property string|null $nama_pemateri
 * @property string $materi_pengembangan
 * @property Carbon $tanggal_pelaksanaan
 * @property string|null $link_materi
 * @property string|null $link_dokumentasi
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'nomor_surat',
    'nama_pemateri',
    'materi_pengembangan',
    'tanggal_pelaksanaan',
    'link_materi',
    'link_dokumentasi',
])]
class MateriPpm extends Model
{
    /** @use HasFactory<MateriPpmFactory> */
    use HasFactory;

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
