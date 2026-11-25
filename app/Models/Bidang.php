<?php

namespace App\Models;

use Database\Factories\BidangFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $bidang
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['bidang'])]
class Bidang extends Model
{
    /** @use HasFactory<BidangFactory> */
    use HasFactory;
}
