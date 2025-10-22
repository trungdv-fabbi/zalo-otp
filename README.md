# Zalo OTP Package for Laravel

A Laravel package for integrating with Zalo OTP API, supporting OTP sending and verification via Zalo.

## Requirements

- PHP >= 8.1
- Laravel >= 9.0
- GuzzleHttp >= 7.0

## Installation

### 1. Install package

```bash
composer require trungdv/zalo-otp
```

### 2. Publish config file

```bash
php artisan vendor:publish --tag=config
```

### 3. Configure in .env

```env
# Zalo OTP Configuration
ZALO_OTP_BASE_URL=https://oauth.zaloapp.com/v4/
ZALO_OTP_BUSINESS_BASE_URL=https://business.openapi.zalo.me/
ZALO_OTP_APP_ID=your_app_id
ZALO_OTP_APP_SECRET=your_app_secret
ZALO_OTP_TIMEOUT=30
ZALO_OTP_RETRY_ATTEMPTS=3
```

## Configuration

The config file is published at `config/zalo-otp.php`:

```php
return [
    'base_url' => env('ZALO_OTP_BASE_URL', 'https://oauth.zaloapp.com/v4/'),
    'bussiness_base_url' => env('ZALO_OTP_BUSINESS_BASE_URL', 'https://business.openapi.zalo.me/'),
    'app_id' => env('ZALO_OTP_APP_ID'),
    'app_secret' => env('ZALO_OTP_APP_SECRET'),
    'timeout' => env('ZALO_OTP_TIMEOUT', 30),
    'retry_attempts' => env('ZALO_OTP_RETRY_ATTEMPTS', 3),
];
```
## config
```
  // bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    TrungDV\ZaloOtp\Providers\ZaloOtpServiceProvider::class,
];
```

## Usage

### sendOtp

```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;

// Get instance
$client = ZaloOtp::getInstance();

// Send OTP
$response = $client->sendOtp([
    'phone' => '0123456789',
    'template_id' => 'your_template_id',
    'template_data' => [
        'otp' => '123456'
    ]
]);

// Get response
echo $response->body();
echo $response->status();
```

### refreshToken

```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;

// Get instance
$client = ZaloOtp::getInstance();

// Set refresh token
$client->setRefreshToken('your_refresh_token');

// Refresh access token
$response = $client->refreshToken();

// Get response
echo $response->body();
echo $response->status();
```

### getMessageStatus

```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;

// Get instance
$client = ZaloOtp::getInstance();

// Get message status
$response = $client->getStatusMessage([
    'message_id' => '1234567890',
    'phone' => '0123456789'
]);

// Get response
echo $response->body();
echo $response->status();
```

## License

MIT License. See [LICENSE](LICENSE) file for more details.

## Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Create Pull Request

## Support

If you encounter any issues, please create an issue on GitHub or contact:

- Email: trungdv@example.com
- GitHub: [@trungdv](https://github.com/trungdv)

## Changelog

### v1.0.0
- Initial release
- Support for sending OTP via Zalo
- Support for refresh token
- Support for getting message status
- Support for HTTP methods (GET, POST, PUT, PATCH)
- Facade support
- Service Container integration
