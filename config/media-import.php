<?php

declare(strict_types=1);

return [
    'wordpress_base' => 'https://yes2broker.in/wp-content/uploads',

    /*
    |--------------------------------------------------------------------------
    | Legacy import tools (disabled in production by default)
    |--------------------------------------------------------------------------
    */
    'import_enabled' => env('WORDPRESS_IMPORT_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Site branding assets (downloaded to public/)
    |--------------------------------------------------------------------------
    */
    'site' => [
        'https://yes2broker.in/wp-content/uploads/2025/07/BLU_LOGO-scaled.png' => 'images/site/logo.png',
        'https://yes2broker.in/wp-content/uploads/2025/07/cropped-Y2B-Final-LOGO_page-0001-Photoroom-768x184-1-e1753419235808.webp' => 'images/site/popup-logo.webp',
        'https://yes2broker.in/wp-content/uploads/2025/07/465465-e1752492172732.png' => 'images/site/logo-footer.png',
        'https://yes2broker.in/wp-content/uploads/2025/07/cropped-cropped-Y2B-Final-LOGO_page-0001-Photoroom-768x184-1-1-32x32.webp' => 'images/site/favicon.webp',
        'https://yes2broker.in/wp-content/uploads/2025/08/ChatGPT-Image-Aug-6-2025-05_31_34-PM.png' => 'images/site/about-image.png',
        'https://yes2broker.in/wp-content/uploads/2025/08/y2b-check.jpg' => 'images/site/about-check.jpg',
        'https://yes2broker.in/wp-content/uploads/2025/08/Untitled-design-30.png' => 'images/site/list-property.png',
        'https://yes2broker.in/wp-content/uploads/2025/08/customers-who-choose-to-buy-a-condominium-room-and-2024-11-01-18-28-48-utc-1024x644.jpg' => 'images/site/channel-partner.jpg',
        'https://yes2broker.in/wp-content/uploads/2025/11/selective-focus-of-paper-houses-and-house-model-on-2024-11-19-04-38-59-utc-1024x683.jpg' => 'images/site/home-loan.jpg',
        'https://yes2broker.in/wp-content/uploads/2025/09/img63-scaled.jpg' => 'images/site/default-property.jpg',
        'https://yes2broker.in/wp-content/uploads/2025/07/yes2broker.mp4' => 'videos/yes2broker.mp4',
    ],

    /*
    |--------------------------------------------------------------------------
    | Partner & bank logos (public/images/media/...)
    |--------------------------------------------------------------------------
    */
    'media' => [
        'https://yes2broker.in/wp-content/uploads/2025/07/shivalik-1.webp' => 'images/media/2025/07/shivalik-1.webp',
        'https://yes2broker.in/wp-content/uploads/2025/07/Shahasya-group-1024x724-1-1.webp' => 'images/media/2025/07/shahasya-group.webp',
        'https://yes2broker.in/wp-content/uploads/2025/07/shree-siddhi-group-1-1.webp' => 'images/media/2025/07/shree-siddhi-group.webp',
        'https://yes2broker.in/wp-content/uploads/2025/07/parshwa-1-1.webp' => 'images/media/2025/07/parshwa.webp',
        'https://yes2broker.in/wp-content/uploads/2025/07/vanshikaa-1024x398-1-1.webp' => 'images/media/2025/07/vanshikaa.webp',
        'https://yes2broker.in/wp-content/uploads/2025/11/urygvhaedurfg-e1763190778158-300x85.png' => 'images/media/2025/11/icici-bank.png',
        'https://yes2broker.in/wp-content/uploads/2025/11/3-e1763189841237-300x71.png' => 'images/media/2025/11/union-bank.png',
        'https://yes2broker.in/wp-content/uploads/2025/11/4-e1763189807220-300x111.png' => 'images/media/2025/11/saraswat-bank.png',
        'https://yes2broker.in/wp-content/uploads/2025/11/khfvbdzkfj-e1763189828278-300x99.png' => 'images/media/2025/11/bajaj-housing.png',
    ],
];
