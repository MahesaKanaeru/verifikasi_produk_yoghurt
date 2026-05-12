<?php

namespace App\Services;


use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class LabelService
{
    private function storagePath(string $relativePath = ''): string
    {
        $base = '/home/cery9751/public_html/vtaya-yoghurt-verify.my.id/storage';
        return $relativePath ? $base . '/' . ltrim($relativePath, '/') : $base;
    }

    private function getConfig(string $ukuran): array
    {
        $ukuran = strtolower($ukuran);

        if (str_contains($ukuran, '200')) {
            return config('label.cup_200');
        }

        return config('label.bottle_250');
    }

    public function mergeQrToLabel(
        $productLabelPath,
        $qrCodePath,
        $productionCode,
        $productionDate,
        $expirationDate,
        string $ukuran = '250 ml (Bottle)'
    ) {
        $manager = new ImageManager(new Driver());

        try {
            $label = $manager->read($this->storagePath($productLabelPath));
            $qr    = $manager->read($this->storagePath($qrCodePath));
        } catch (\Exception $e) {
            return null;
        }

        $cfg      = $this->getConfig($ukuran);
        $dpi      = 300;
        $cmToInch = 1 / 2.54;

        $targetW    = (int) ($cfg['label_width_cm']  * $cmToInch * $dpi);
        $targetH    = (int) ($cfg['label_height_cm'] * $cmToInch * $dpi);
        $qrSize     = (int) ($cfg['qr_size_cm']      * $cmToInch * $dpi);
        $posX       = (int) ($cfg['qr_pos_x_cm']     * $cmToInch * $dpi);
        $posY       = (int) ($cfg['qr_pos_y_cm']     * $cmToInch * $dpi);
        $centerX    = (int) ($cfg['center_x_cm']     * $cmToInch * $dpi);
        $prodY      = (int) ($cfg['prod_pos_y_cm']   * $cmToInch * $dpi);
        $expY       = (int) ($prodY + ($cfg['exp_gap_cm'] * $cmToInch * $dpi));
        $fontSizePx = (int) $cfg['font_size'];

        $label->resize($targetW, $targetH);
        $qr->resize($qrSize, $qrSize);
        $label->place($qr, 'top-left', $posX, $posY);

        $prodText = 'PROD : ' . date('Ymd', strtotime($productionDate));
        $expText  = 'EXP : '  . date('Ymd', strtotime($expirationDate));

        $fontPath = $this->storagePath('fonts/Montserrat.ttf');

        $label->text($prodText, $centerX, $prodY, function ($font) use ($fontSizePx, $fontPath) {
            $font->file($fontPath);
            $font->size($fontSizePx);
            $font->color('#000000');
            $font->align('center');
            $font->valign('top');
        });

        $label->text($expText, $centerX, $expY, function ($font) use ($fontSizePx, $fontPath) {
            $font->file($fontPath);
            $font->size($fontSizePx);
            $font->color('#000000');
            $font->align('center');
            $font->valign('top');
        });

        $unique = time() . '_' . uniqid();
        $filename = $productionCode . '_' . $unique . '_label.png';
        $folder   = $this->storagePath('final_labels');

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $label->save($folder . DIRECTORY_SEPARATOR . $filename, 100);

        return 'final_labels/' . $filename;
    }
}

// old
// use Intervention\Image\ImageManager;
// use Intervention\Image\Drivers\Gd\Driver;
// use Illuminate\Support\Facades\Storage;

// class LabelService
// {
//     /**
//      * Pilih config label berdasarkan ukuran produk.
//      * Tambahkan kondisi baru di sini kalau ada ukuran lain.
//      */
//     private function getConfig(string $ukuran): array
//     {
//         $ukuran = strtolower($ukuran);

//         if (str_contains($ukuran, '200')) {
//             return config('label.cup_200');
//         }

//         // Default: 250ml bottle
//         return config('label.bottle_250');
//     }

//     public function mergeQrToLabel($productLabelPath, $qrCodePath, $productionCode, $productionDate, $expirationDate, string $ukuran = '250 ml (Bottle)')
//     {
//         $manager = new ImageManager(new Driver());

//         try {
//             $label = $manager->read(Storage::disk('public')->path($productLabelPath));
//             $qr    = $manager->read(Storage::disk('public')->path($qrCodePath));
//         } catch (\Exception $e) {
//             return null;
//         }

//         // Ambil config sesuai ukuran
//         $cfg        = $this->getConfig($ukuran);
//         $dpi        = 300;
//         $cmToInch   = 1 / 2.54;

//         $targetW    = (int) ($cfg['label_width_cm']  * $cmToInch * $dpi);
//         $targetH    = (int) ($cfg['label_height_cm'] * $cmToInch * $dpi);
//         $qrSize     = (int) ($cfg['qr_size_cm']      * $cmToInch * $dpi);
//         $posX       = (int) ($cfg['qr_pos_x_cm']     * $cmToInch * $dpi);
//         $posY       = (int) ($cfg['qr_pos_y_cm']      * $cmToInch * $dpi);
//         $centerX    = (int) ($cfg['center_x_cm']     * $cmToInch * $dpi);
//         $prodY      = (int) ($cfg['prod_pos_y_cm']   * $cmToInch * $dpi);
//         $expY       = (int) ($prodY + ($cfg['exp_gap_cm'] * $cmToInch * $dpi));
//         $fontSizePx = (int) $cfg['font_size'];

//         $label->resize($targetW, $targetH);
//         $qr->resize($qrSize, $qrSize);
//         $label->place($qr, 'top-left', $posX, $posY);

//         $prodText = 'PROD : ' . date('Ymd', strtotime($productionDate));
//         $expText  = 'EXP : '  . date('Ymd', strtotime($expirationDate));

//         $label->text($prodText, $centerX, $prodY, function ($font) use ($fontSizePx) {
//             $font->file(storage_path('app/public/fonts/Montserrat.ttf'));
//             $font->size($fontSizePx);
//             $font->color('#000000');
//             $font->align('center');
//             $font->valign('top');
//         });

//         $label->text($expText, $centerX, $expY, function ($font) use ($fontSizePx) {
//             $font->file(storage_path('app/public/fonts/Montserrat.ttf'));
//             $font->size($fontSizePx);
//             $font->color('#000000');
//             $font->align('center');
//             $font->valign('top');
//         });

//         $fileName = 'final_labels/' . $productionCode . '_label.png';

//         $publicStoragePath = public_path('storage/final_labels');

//         if (!file_exists($publicStoragePath)) {
//             mkdir($publicStoragePath, 0777, true);
//         }

//         $label->save(
//             public_path('storage/' . $fileName),
//             100
//         );

//         return $fileName;
//     }
// }