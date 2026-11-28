<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Login Wallpaper
    |--------------------------------------------------------------------------
    |
    | A random nature/landscape photo is shown behind the login card. Images
    | come from Lorem Picsum, which serves photos published under the
    | Unsplash license. The IDs below are curated landscape photos, so the
    | wallpaper never falls back to portraits or random objects. Set
    | LOGIN_WALLPAPER_ENABLED to false to keep the default plain
    | background, or set LOGIN_WALLPAPER_URL to use a fixed image.
    |
    */

    'wallpaper' => [
        'enabled' => env('LOGIN_WALLPAPER_ENABLED', true),
        'url' => env('LOGIN_WALLPAPER_URL'),
        'provider' => env('LOGIN_WALLPAPER_PROVIDER', 'https://picsum.photos'),
        'blur' => (int) env('LOGIN_WALLPAPER_BLUR', 2),

        'ids' => [
            10, 11, 12, 13, 14, 15, 16, 17, 18,
            28, 29, 33, 37, 44, 46,
            52, 59, 62, 66, 70, 71,
            83, 85, 92, 93, 94, 95, 98,
            108, 110, 112, 116, 118, 125, 127, 128,
            132, 135, 136, 140, 141, 147,
        ],
    ],

];
