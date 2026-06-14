<?php
return [

    // ── 250 ml BOTTLE ─────────────────────────────────────
    'bottle_250' => [
        'label_width_cm'  => 17.0,
        'label_height_cm' => 15.5,
        'qr_pos_x_cm'     => 13.35,
        'qr_pos_y_cm'     => 10.45,
        'qr_size_cm'      => 2.0,
        'center_x_cm'     => 14.35,
        'prod_pos_y_cm'   => 12.54,
        'exp_gap_cm'      => 0.40,
        'font_size'       => 24,
    ],

    // ── 200 ml CUP ────────────────────────────────────────
    'cup_200' => [
        'label_width_cm'  => 6.0,
        'label_height_cm' => 6.0,
        'qr_pos_x_cm'     => 4.53,
        'qr_pos_y_cm'     => 2.5,
        'qr_size_cm'      => 1.0,
        'center_x_cm'     => 4.99,
        'prod_pos_y_cm'   => 2.04,
        'exp_gap_cm'      => 0.20,
        'font_size'       => 14,
    ],

    'allowed_format' => ['jpg', 'jpeg', 'png'],
    'aes_key'        => env('AES_KEY', 'VTAYAYOGHURTASLI'),
];