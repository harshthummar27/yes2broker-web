<?php

return [

    'leadplus' => [
        'enabled' => env('LEADPLUS_ENABLED', false),
        'url' => env(
            'LEADPLUS_API_URL',
            'https://yesbroker.leadpluss.com/Services/api/TenantLeads/Submit'
        ),
        'vendor_key' => env('LEADPLUS_VENDOR_KEY'),
        'isd' => env('LEADPLUS_ISD', '91'),
        'default_lead_source' => env('LEADPLUS_LEAD_SOURCE', 'Website'),
        'default_city' => env('LEADPLUS_DEFAULT_CITY', 'Ahmedabad'),
        'default_state' => env('LEADPLUS_DEFAULT_STATE', 'Gujarat'),
        'timeout' => (int) env('LEADPLUS_TIMEOUT', 15),
    ],

];
