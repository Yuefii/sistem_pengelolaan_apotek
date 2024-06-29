<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventaris extends Model
{
    use HasFactory;
    protected $fillable = [
        'obat_id',
        'stok_obat',
        'tanggal',
    ];

    protected static function booted()
    {
        static::created(function ($inventaris) {
            $obat = $inventaris->obat;
            if ($obat) {
                $obat->total_stok_obat += $inventaris->stok_obat;
                $obat->save();
            }
        });
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
