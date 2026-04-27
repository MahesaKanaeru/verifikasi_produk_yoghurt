<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Illuminate\Support\Facades\Storage;

class QrService
{
    public function generate($productionCode, $encryptedText)
    {
        // ── Isi QR = deep link langsung ke welcome + query ?scan= ──────
        // Kenapa pakai query string bukan /v/{path}?
        // Karena encrypted text mengandung karakter +, /, = yang akan
        // merusak URL path dan menyebabkan 404 Not Found di Laravel router.
        // Query string handle karakter khusus dengan benar via urlencode().
        // Format: https://domain.com/?scan=ENCRYPTED
        $deepLink = rtrim(config('app.url'), '/') . '/?scan=' . urlencode($encryptedText);

        $options = new QROptions([
            'outputInterface'  => QRGdImagePNG::class,
            'outputBase64'     => false,
            'eccLevel'         => 'M',   // M agar tahan error walau URL agak panjang
            'scale'            => 5,
            'imageTransparent' => false,
            'cachefile'        => null,
        ]);

        $qrImage  = (new QRCode($options))->render($deepLink);
        $fileName = 'qr_codes/' . $productionCode . '.png';

        Storage::disk('public')->put($fileName, $qrImage);

        return $fileName;
    }
}