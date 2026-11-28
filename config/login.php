<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Login Wallpaper
    |--------------------------------------------------------------------------
    |
    | A random, freely licensed photo is shown behind the login card. Images
    | come from Lorem Picsum, which serves photos published under the
    | Unsplash license. Set LOGIN_WALLPAPER_ENABLED to false to keep the
    | default plain background, or set LOGIN_WALLPAPER_URL to use a
    | fixed image instead of a random one.
    |
    */

    'wallpaper' => [
        'enabled' => env('LOGIN_WALLPAPER_ENABLED', true),
        'url' => env('LOGIN_WALLPAPER_URL'),
        'provider' => env('LOGIN_WALLPAPER_PROVIDER', 'https://picsum.photos'),
        'blur' => (int) env('LOGIN_WALLPAPER_BLUR', 2),
    ],

];
