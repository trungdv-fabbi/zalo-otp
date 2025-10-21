<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zalo OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho Zalo OTP API
    |
    */

    'base_url' => env('ZALO_OTP_BASE_URL', 'https://oauth.zaloapp.com/v4/'),

    'bussiness_base_url' => env('ZALO_OTP_BUSINESS_BASE_URL', 'https://business.openapi.zalo.me/'),

    'app_id' => env('ZALO_OTP_APP_ID'),

    'app_secret' => env('ZALO_OTP_APP_SECRET'),

    'timeout' => env('ZALO_OTP_TIMEOUT', 30),

    'retry_attempts' => env('ZALO_OTP_RETRY_ATTEMPTS', 3),
];

