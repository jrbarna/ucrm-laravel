<?php

return [
    'url_base' => env('UCRM_URL_BASE','https://ucrm.example.com'),
    'url_path' => env('UCRM_URL_PATH','/api/v1.0/'),
    'token' => env('UCRM_TOKEN'),
    'payment_method' => env('UCRM_PAYMENT_METHOD'),
    'payment_user_id' => env('UCRM_PAYMENT_USER_ID'),
];

