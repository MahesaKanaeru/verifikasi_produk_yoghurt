<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'ukuran',
        'estimasi_expired',
        'foto_produk',
        'foto_label',
    ];
public static function generateKode()
{
    $last = self::orderBy('id', 'desc')->first();
    if (!$last) return 'PRD001';
    $number = str_replace('PRD', '', $last->kode_produk);
    return 'PRD' . sprintf('%03d', (int)$number + 1);
}
}