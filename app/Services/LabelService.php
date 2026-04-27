<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class LabelService
{
    public function mergeQrToLabel($productLabelPath, $qrCodePath, $productionCode, $productionDate, $expirationDate)
    {
        $manager = new ImageManager(new Driver());
        try {
            $label = $manager->read(Storage::disk('public')->path($productLabelPath));
            $qr = $manager->read(Storage::disk('public')->path($qrCodePath));
        } catch (\Exception $e) {
            return null;
        }

        $dpi = 300;
        $cmToInch = 1 / 2.54;

        $targetW = (config('label.label_width_cm') * $cmToInch) * $dpi; 
        $targetH = (config('label.label_height_cm') * $cmToInch) * $dpi;
        $qrSize  = (config('label.qr_size_cm') * $cmToInch) * $dpi;     
        $posX    = (config('label.qr_pos_x_cm') * $cmToInch) * $dpi;   
        $posY    = (config('label.qr_pos_y_cm') * $cmToInch) * $dpi; 

        $label->resize((int)$targetW, (int)$targetH); 
        $qr->resize((int)$qrSize, (int)$qrSize);
        $label->place($qr, 'top-left', (int)$posX, (int)$posY);
        // konversi font size
        $fontSizePx = config('label.font_size');

        $centerX = (config('label.center_x_cm') * $cmToInch) * $dpi;
        $prodY   = (config('label.prod_pos_y_cm') * $cmToInch) * $dpi;
        $expY    = $prodY + ((config('label.exp_gap_cm') * $cmToInch) * $dpi);

        $prodText = 'PROD : ' . date('Ymd', strtotime($productionDate));
        $expText  = 'EXP : ' . date('Ymd', strtotime($expirationDate));

        // PROD (CENTER)
        $label->text($prodText, (int)$centerX, (int)$prodY, function ($font) use ($fontSizePx) {
            $font->file(storage_path('app/public/fonts/Montserrat.ttf'));
            $font->size((int)$fontSizePx);
            $font->color('#000000');
            $font->align('center');   // 🔥 ini kunci
            $font->valign('top');
        });

        // EXP (CENTER)
        $label->text($expText, (int)$centerX, (int)$expY, function ($font) use ($fontSizePx) {
            $font->file(storage_path('app/public/fonts/Montserrat.ttf'));
            $font->size((int)$fontSizePx);
            $font->color('#000000');
            $font->align('center');   // 🔥 ini kunci
            $font->valign('top');
        });
        $fileName = 'final_labels/' . $productionCode . '_label.png';

        if (!Storage::disk('public')->exists('final_labels')) {
            Storage::disk('public')->makeDirectory('final_labels');
        }
        $label->save(Storage::disk('public')->path($fileName), 100);
        return $fileName;
    }
}