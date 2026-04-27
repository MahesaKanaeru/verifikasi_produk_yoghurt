<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_code', 'product_id', 'production_date', 
        'expiration_date', 'encrypted_text', 'qr_code_path', 'final_label_path'
    ];

    // Karena tipe datanya date, kita cast agar otomatis jadi objek Carbon
    protected $casts = [
        'production_date' => 'date',
        'expiration_date' => 'date',
    ];

    // Relasi ke tabel Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Generate otomatis VY00001
    public static function generateProductionCode()
    {
        $last = self::orderBy('id', 'desc')->first();
        if (!$last) return 'VY00001';
        
        $number = str_replace('VY', '', $last->production_code);
        return 'VY' . sprintf('%05d', (int)$number + 1);
    }

    // Helper untuk Blade: Cek apakah sudah expired
    public function isExpired()
    {
        return Carbon::now()->startOfDay()->greaterThan($this->expiration_date);
    }

    // Helper untuk Blade: Hitung sisa hari
    public function getDaysLeftAttribute()
    {
        return Carbon::now()->startOfDay()->diffInDays($this->expiration_date, false);
    }

    // Accessor untuk id_produksi (alias production_code)
    public function getIdProduksiAttribute()
    {
        return $this->production_code;
    }

    // Helper untuk Blade: Cek apakah mendekati expired (H-7)
    public function isNearExpiry()
    {
        return $this->days_left <= 7 && $this->days_left >= 0;
    }
}