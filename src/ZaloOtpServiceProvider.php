<?php

namespace TrungDV\ZaloOtp;

use Illuminate\Support\ServiceProvider;
use TrungDV\ZaloOtp\Services\ZaloOtpService;

class ZaloOtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ZaloOtpService::class, function ($app) {
            return new ZaloOtpService();
        });

        $this->app->alias(ZaloOtpService::class, 'zalo-otp');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/zalo-otp.php' => config_path('zalo-otp.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/zalo-otp.php', 'zalo-otp'
        );
    }
}
