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
        'total_harga'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transaction) {
            $obat = $transaction->obat;
            $harga_obat = $obat->harga_obat;
            $transaction->total_harga = $transaction->jumlah * $harga_obat;

            if ($transaction->jumlah > $obat->total_stok_obat) {
                throw new \Exception('Jumlah transaksi melebihi stok obat yang tersedia.');
            }
        });
        static::created(function ($transaction) {
            $obat = $transaction->obat;
            $obat->total_stok_obat -= $transaction->jumlah;
            $obat->save();
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
