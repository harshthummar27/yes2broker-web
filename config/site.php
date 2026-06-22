<?php

return [
    'name' => 'Yes2Broker',
    'tagline' => 'Real Estate in Ahmedabad',
    'email' => 'contact@y2b.in',
    'phone' => '+91 95125 98980',
    'phone_href' => 'tel:+919512598980',
    'whatsapp_href' => 'https://wa.me/919512598980',
    'address' => '401 Amrakunj Avis Nigam nagar Near Tapovan Circle nagar, Chandkheda Ahmedabad, GUJARAT 382424',
    'maps_url' => 'https://www.google.com/maps/search/?api=1&query=401+Amrakunj+Avis+Nigam+nagar+Near+Tapovan+Circle+nagar+Chandkheda+Ahmedabad+Gujarat+382424',
    'maps_embed_url' => env(
        'MAPS_EMBED_URL',
        'https://maps.google.com/maps?q=401+Amrakunj+Avis+Nigam+nagar+Near+Tapovan+Circle+nagar,+Chandkheda+Ahmedabad,+GUJARAT+382424&t=m&z=10&output=embed&iwloc=near'
    ),
    'street_view_embed_url' => env(
        'STREET_VIEW_EMBED_URL',
        'https://maps.google.com/maps?layer=c&cbll=23.1176112,72.6117431&cbp=11,0,0,0,0&output=svembed'
    ),
    'maps_directions_url' => 'https://www.google.com/maps/dir//Aamrakunj+Avis+Near,+Tapovan+Cir+Nigam+Nagar,+Chandkheda+Ahmedabad,+Gujarat+382424/@23.1176112,72.6117431,10z',

    // Relative paths under public/ — use site_asset() in views.
    'media_url' => 'images/media',
    'favicon' => 'images/site/favicon.webp',
    'hero_video' => 'videos/yes2broker.mp4',
    'logo' => 'images/site/logo.png',
    'popup_logo' => 'images/site/popup-logo.webp',
    'logo_footer' => 'images/site/logo-footer.png',
    'about_image' => 'images/site/about-image.png',
    'about_check_image' => 'images/site/about-check.jpg',
    'list_property_image' => 'images/site/list-property.png',
    'channel_partner_image' => 'images/site/channel-partner.jpg',
    'home_loan_image' => 'images/site/home-loan.jpg',
    'default_property_image' => 'images/site/default-property.jpg',

    'social' => [
        'facebook' => 'https://www.facebook.com/yes2broker',
        'instagram' => 'https://www.instagram.com/yes2broker/',
        'linkedin' => 'https://www.linkedin.com/company/yes2broker/',
    ],
    'marquee' => [
        "India's first broking house offering ₹1,00,000 cashback on purchasing property on a woman's name.",
        'Lowest Price Guarantee',
        'Dedicated Relationship Manager',
    ],
];
