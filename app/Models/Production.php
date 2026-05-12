<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Production extends Model
{
    protected $fillable = [
        'production_number',
        'production_code',
        'product_id',
        'qty',
        'production_date',
        'expiration_date',
        'qr_code_path',
        'final_label_path',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function generateProductionCode(): string
    {
        $latest = self::orderBy('id', 'desc')->value('production_number');

        if (! $latest) {
            return 'VY00001';
        }

        $number = (int) substr($latest, 2);

        return 'VY'.str_pad($number + 1, 5, '0', STR_PAD_LEFT);
    }
}
