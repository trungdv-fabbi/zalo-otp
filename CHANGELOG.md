# Changelog

Tất cả các thay đổi quan trọng của package này sẽ được ghi lại trong file này.

Format dựa trên [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
và package này tuân thủ [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Hỗ trợ Laravel 12.x
- Method `formatPhoneNumber()` để format số điện thoại
- Method `setRequestUrl()` với hỗ trợ URL đầy đủ và endpoint
- Method `setAuthorization()` để thiết lập access token
- Method `setRefreshToken()` để thiết lập refresh token
- Method `getStatusMessage()` để lấy trạng thái message
- HTTP methods thông qua `__call` magic method
- Singleton pattern với `getInstance()`

### Changed
- Cải thiện error handling trong `getJson()` method
- Tối ưu hóa URL handling trong `setRequestUrl()`
- Cải thiện logic trong `formatParams()` method

### Fixed
- Sửa lỗi URL duplicate khi sử dụng `setRequestUrl()`
- Sửa lỗi logic trong `formatParams()` method
- Sửa lỗi type declaration cho `$app_id` và `$app_secret`

## [1.0.0] - 2024-01-15

### Added
- Initial release
- Hỗ trợ gửi OTP qua Zalo API
- Hỗ trợ refresh access token
- Hỗ trợ lấy trạng thái message
- Facade `ZaloOtp` để sử dụng dễ dàng
- Service Provider để đăng ký với Laravel
- Configuration file với các tùy chọn cấu hình
- Hỗ trợ HTTP methods (GET, POST, PUT, PATCH)
- Hỗ trợ custom headers và parameters
- Error handling và logging
- Test cases cơ bản
- Documentation đầy đủ

### Features
- **ZaloClient**: Class chính để tương tác với Zalo API
- **ZaloOtp Facade**: Facade để sử dụng dễ dàng
- **ZaloOtpServiceProvider**: Service Provider cho Laravel
- **Configuration**: File cấu hình với environment variables
- **ZaloUri**: Constants cho các API endpoints
- **ErrorType**: Enum cho các loại lỗi

### Requirements
- PHP >= 8.1
- Laravel >= 9.0
- GuzzleHttp >= 7.0

### Installation
```bash
composer require trungdv/zalo-otp
php artisan vendor:publish --tag=config
```

### Usage
```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;

$client = ZaloOtp::getInstance();
$response = $client->sendOtp([
    'phone' => '0123456789',
    'message' => 'Mã OTP của bạn là: {otp_code}'
]);
```
