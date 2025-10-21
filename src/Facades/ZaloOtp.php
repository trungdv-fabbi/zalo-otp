<?php

namespace TrungDV\ZaloOtp\Facades;

use Illuminate\Support\Facades\Facade;
use TrungDV\ZaloOtp\Services\ZaloOtpService;

/**
 * @method static array sendOtp(string $phoneNumber, string $message = null)
 * @method static array verifyOtp(string $phoneNumber, string $otpCode)
 * @method static array getOtpInfo(string $phoneNumber)
 */
class ZaloOtp extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'zalo-otp';
    }
}

