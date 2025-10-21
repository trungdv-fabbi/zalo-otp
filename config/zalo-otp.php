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

    'base_url' => env('ZALO_OTP_BASE_URL', 'https://openapi.zalo.me/v2.0/'),
    
    'app_id' => env('ZALO_OTP_APP_ID'),
    
    'app_secret' => env('ZALO_OTP_APP_SECRET'),
    
    'timeout' => env('ZALO_OTP_TIMEOUT', 30),
    
    'retry_attempts' => env('ZALO_OTP_RETRY_ATTEMPTS', 3),
    
    'default_message' => env('ZALO_OTP_DEFAULT_MESSAGE', 'Mã OTP của bạn là: {otp_code}'),
];

