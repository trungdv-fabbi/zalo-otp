<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZaloOtpController;

/*
|--------------------------------------------------------------------------
| Zalo OTP API Routes
|--------------------------------------------------------------------------
|
| Example routes for Zalo OTP package
|
*/

// Public routes (no authentication required)
Route::prefix('api/zalo-otp')->group(function () {
    // Send OTP
    Route::post('/send', [ZaloOtpController::class, 'sendOtp']);

    // Verify OTP
    Route::post('/verify', [ZaloOtpController::class, 'verifyOtp']);

    // Get OTP info
    Route::get('/info', [ZaloOtpController::class, 'getOtpInfo']);

    // Refresh token
    Route::post('/refresh-token', [ZaloOtpController::class, 'refreshToken']);

    // Get message status
    Route::get('/message-status', [ZaloOtpController::class, 'getMessageStatus']);
});

// Protected routes (authentication required)
Route::middleware('auth:api')->prefix('api/zalo-otp')->group(function () {
    // Admin routes
    Route::get('/admin/stats', function () {
        // Get OTP statistics
        return response()->json(['message' => 'Admin stats endpoint']);
    });

    Route::get('/admin/logs', function () {
        // Get OTP logs
        return response()->json(['message' => 'Admin logs endpoint']);
    });
});

/*
|--------------------------------------------------------------------------
| Example API Usage
|--------------------------------------------------------------------------
|
| POST /api/zalo-otp/send
| {
|     "phone": "0123456789",
|     "message": "Mã OTP của bạn là: {otp_code}"
| }
|
| POST /api/zalo-otp/verify
| {
|     "phone": "0123456789",
|     "otp_code": "123456"
| }
|
| GET /api/zalo-otp/info?phone=0123456789
|
| POST /api/zalo-otp/refresh-token
| {
|     "refresh_token": "your_refresh_token"
| }
|
| GET /api/zalo-otp/message-status?message_id=1234567890&phone=0123456789
|
*/
