# Zalo OTP Package for Laravel

A Laravel package for integrating with Zalo ZNS (Zalo Notification Service) API, enabling you to send OTP and transactional messages via Zalo platform.

## Features

- 🚀 Easy integration with Laravel applications
- 📱 Send OTP messages via Zalo ZNS
- 🔄 Access token refresh functionality
- 📊 Message delivery status tracking
- ⚡ Automatic error handling with custom exceptions
- 🛡️ Type-safe error codes
- 🔧 Configurable timeout and retry settings

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

## Usage

### Sending OTP

Send an OTP message to a phone number using a pre-configured Zalo ZNS template.

```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;
use TrungDV\ZaloOtp\Exceptions\ZaloException;

try {
    // Get instance
    $client = ZaloOtp::getInstance();
    
    // Send OTP
    $result = $client
        ->setAuthorization('your_access_token')
        ->sendOtp([
            'phone' => '0123456789',
            'template_id' => 'your_template_id',
            'template_data' => [
                'otp' => '123456',
                'expire_time' => '5 phút'
            ]
        ]);
    
    // Handle successful response
    echo "Message sent successfully!";
    echo "Message ID: " . $result['data']['msg_id'];
    
} catch (ZaloException $e) {
    // Handle Zalo API errors
    echo "Error Code: " . $e->getZaloErrorCode();
    echo "Error Message: " . $e->getMessage();
    echo "Error Detail: " . $e->getZaloErrorDetail();
}
```

#### Testing Mode

If you want to test sending messages without actually delivering them to users, you can add the `"mode": "development"` option in the params when calling `sendOtp()`. 

**Important Note:** The development mode will only work when the recipient phone number has an admin role in your Zalo Official Account. Regular phone numbers will not receive messages in development mode.

```php
try {
    $result = $client
        ->setAuthorization('your_access_token')
        ->sendOtp([
            'phone' => '0123456789', // Must be an admin phone number
            'mode' => 'development', // Testing mode
            'template_id' => 'your_template_id',
            'template_data' => [
                'otp' => '123456'
            ]
        ]);
} catch (ZaloException $e) {
    // Handle errors
}
```

### Refreshing Access Token

Refresh your Zalo access token using a refresh token.

```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;
use TrungDV\ZaloOtp\Exceptions\ZaloException;

try {
    // Get instance
    $client = ZaloOtp::getInstance();
    
    // Set refresh token and refresh
    $result = $client
        ->setRefreshToken('your_refresh_token')
        ->refreshToken();
    
    // Get new access token
    $newAccessToken = $result['access_token'];
    $newRefreshToken = $result['refresh_token'];
    $expiresIn = $result['expires_in'];
    
    // Store tokens for future use
    // ...
    
} catch (ZaloException $e) {
    // Handle token refresh errors
    echo "Failed to refresh token: " . $e->getMessage();
}
```

### Checking Message Status

Check the delivery status of a sent message.

```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;
use TrungDV\ZaloOtp\Exceptions\ZaloException;

try {
    // Get instance
    $client = ZaloOtp::getInstance();
    
    // Get message status
    $result = $client
        ->setAuthorization('your_access_token')
        ->getStatusMessage([
            'message_id' => '1234567890',
            'phone' => '0123456789'
        ]);
    
    // Check delivery status
    $status = $result['data']['status'];
    $sentTime = $result['data']['sent_time'];
    
    echo "Message Status: " . $status;
    
} catch (ZaloException $e) {
    // Handle errors
    echo "Failed to get message status: " . $e->getMessage();
}
```

## Error Handling

The package provides comprehensive error handling through the `ZaloException` class. All API methods automatically validate responses and throw exceptions when errors occur.

### Exception Methods

```php
try {
    $result = $client->sendOtp([...]);
} catch (ZaloException $e) {
    // Get Zalo error code (integer)
    $errorCode = $e->getZaloErrorCode();
    
    // Get user-friendly error message (in Vietnamese)
    $message = $e->getMessage();
    
    // Get detailed error message from Zalo API
    $detail = $e->getZaloErrorDetail();
    
    // Get all error data as array
    $errorData = $e->toArray();
    /*
    [
        'zalo_error_code' => -108,
        'zalo_error_detail' => 'Phone number is invalid',
        'message' => 'Số điện thoại không hợp lệ'
    ]
    */
}
```

### Common Error Codes

| Error Code | Description |
|------------|-------------|
| `0` | Success |
| `-100` | Unknown error |
| `-108` | Invalid phone number |
| `-115` | Out of quota (insufficient balance) |
| `-124` | Invalid access token |
| `-144` | Daily ZNS quota exceeded |

For a complete list of error codes, check the `ZaloErrorCode` enum in `src/Consts/ZaloErrorCode.php`.

## Response Format

All methods return an array containing the parsed JSON response from Zalo API:

### Successful Response Example

```php
[
    'error' => 0,
    'message' => 'Success',
    'data' => [
        'msg_id' => '1234567890',
        'sent_time' => '2024-01-01 12:00:00',
        // ... other data
    ]
]
```

## Advanced Usage

### Phone Number Formatting

The package automatically formats Vietnamese phone numbers from `0xxx` format to `84xxx` format:

```php
// Both formats are accepted
$client->sendOtp(['phone' => '0123456789', ...]); // Automatically converts to 84123456789
$client->sendOtp(['phone' => '84123456789', ...]); // Already in correct format
```

### Custom Headers

You can set custom headers for your requests:

```php
$client->setHeaders([
    'Custom-Header' => 'value'
]);
```

### Debug Mode

For debugging purposes, you can generate a cURL command from your requests:

```php
$curlCommand = $client->toCurlFromPendingRequest(
    $request,
    'POST',
    'https://api.zalo.me/endpoint',
    ['param' => 'value']
);
echo $curlCommand;
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

If you encounter any issues or have questions, please:

- Create an issue on [GitHub Issues](https://github.com/trungdv-fabbi/zalo-otp/issues)
- Contact: trungdv@fabbi.io

## Testing

Before using in production, test your integration in development mode:

```php
// Send to admin phone numbers only
$result = $client->sendOtp([
    'phone' => 'admin_phone',
    'mode' => 'development',
    // ...
]);
```

## Security

- Never commit your `.env` file or expose your `ZALO_OTP_APP_SECRET`
- Store access tokens securely (database with encryption, cache with proper TTL)
- Implement rate limiting for OTP sending to prevent abuse
- Validate phone numbers before sending OTP

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

### Latest Changes

**v1.0.4**
- ✨ Added automatic error handling with `ZaloException`
- ✨ Integrated error handling directly into `ZaloClient`
- 🔥 Removed separate helpers file
- 📝 Improved documentation with comprehensive examples
- 🛡️ Type-safe error codes with `ZaloErrorCode` enum

**v1.0.0**
- 🎉 Initial release
- ✅ Send OTP via Zalo ZNS
- ✅ Refresh access token
- ✅ Get message delivery status
- ✅ Support for HTTP methods (GET, POST, PUT, PATCH)
- ✅ Facade support
- ✅ Service Container integration
