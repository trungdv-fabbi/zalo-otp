<?php

namespace TrungDV\ZaloOtp\Providers;

use Illuminate\Support\ServiceProvider;
use TrungDV\ZaloOtp\ZaloClient;

class ZaloOtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('zalo-otp', function() {
            return new ZaloClient;
        });
        $this->mergeConfigFrom(
            __DIR__.'/../config/zalo-otp.php', 'zalo-otp'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/zalo-otp.php' => config_path('zalo-otp.php'),
        ], 'config');
    }
}
