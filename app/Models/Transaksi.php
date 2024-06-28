<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'obat_id',
        'jumlah',
        'tanggal',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($transaksi) {
            $inventaris = Inventaris::where('obat_id', $transaksi->obat_id)->first();

            if ($inventaris) {
                if ($inventaris->stok_obat < $transaksi->jumlah) {
                    throw new \Exception('Stok obat tidak mencukupi.');
                }
                $inventaris->stok_obat -= $transaksi->jumlah;
                $inventaris->save();
            }
        });
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
