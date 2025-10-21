<?php

namespace TrungDV\ZaloOtp\Facades;

use Illuminate\Support\Facades\Facade;

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

