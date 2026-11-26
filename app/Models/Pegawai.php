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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
 * @property-read int|null $diklats_count
 * @property-read int|null $ppms_count
 * @property-read int|null $seminars_count
 * @property-read int|null $webinars_count
 * @property-read int|null $lcs_count
 * @property-read int|null $belajar_mandiris_count
 * @property-read int|null $mentorings_count
 * @property-read int|null $coachings_count
 * @property-read int|null $workshops_count
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
     * @var list<string>
     */
    public const KEGIATAN_RELATIONS = [
        'diklats',
        'ppms',
        'seminars',
        'webinars',
        'lcs',
        'belajarMandiris',
        'mentorings',
        'coachings',
        'workshops',
    ];

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
     * @return HasMany<Diklat, $this>
     */
    public function diklats(): HasMany
    {
        return $this->hasMany(Diklat::class);
    }

    /**
     * @return HasMany<Ppm, $this>
     */
    public function ppms(): HasMany
    {
        return $this->hasMany(Ppm::class);
    }

    /**
     * @return HasMany<Seminar, $this>
     */
    public function seminars(): HasMany
    {
        return $this->hasMany(Seminar::class);
    }

    /**
     * @return HasMany<Webinar, $this>
     */
    public function webinars(): HasMany
    {
        return $this->hasMany(Webinar::class);
    }

    /**
     * @return HasMany<Lc, $this>
     */
    public function lcs(): HasMany
    {
        return $this->hasMany(Lc::class);
    }

    /**
     * @return HasMany<BelajarMandiri, $this>
     */
    public function belajarMandiris(): HasMany
    {
        return $this->hasMany(BelajarMandiri::class);
    }

    /**
     * @return HasMany<Mentoring, $this>
     */
    public function mentorings(): HasMany
    {
        return $this->hasMany(Mentoring::class);
    }

    /**
     * @return HasMany<Coaching, $this>
     */
    public function coachings(): HasMany
    {
        return $this->hasMany(Coaching::class);
    }

    /**
     * @return HasMany<Workshop, $this>
     */
    public function workshops(): HasMany
    {
        return $this->hasMany(Workshop::class);
    }

    /**
     * Determine whether the pegawai still has any kegiatan records.
     */
    public function hasKegiatan(): bool
    {
        foreach (self::KEGIATAN_RELATIONS as $relation) {
            $countAttribute = Str::snake($relation).'_count';

            if (array_key_exists($countAttribute, $this->attributes)) {
                if ((int) $this->attributes[$countAttribute] > 0) {
                    return true;
                }

                continue;
            }

            if ($this->{$relation}()->exists()) {
                return true;
            }
        }

        return false;
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
