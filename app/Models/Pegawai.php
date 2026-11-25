<?php

namespace App\Models;

use Database\Factories\PegawaiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property string $nip
 * @property int $bidang_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bidang $bidang
 */
#[Fillable(['nama', 'nip', 'bidang_id'])]
class Pegawai extends Model
{
    /** @use HasFactory<PegawaiFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Bidang, $this>
     */
    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }
}
