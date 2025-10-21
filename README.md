# Zalo OTP Package

Package Laravel để gửi và xác thực OTP qua Zalo API.

## Cài đặt

### 1. Cài đặt qua Composer

```bash
composer require trungdv/zalo-otp
```

### 2. Đăng ký Service Provider

Thêm vào `config/app.php`:

```php
'providers' => [
    // ...
    TrungDV\ZaloOtp\ZaloOtpServiceProvider::class,
],
```

### 3. Publish config file

```bash
php artisan vendor:publish --provider="TrungDV\ZaloOtp\ZaloOtpServiceProvider" --tag="config"
```

### 4. Cấu hình environment

Thêm vào `.env`:

```env
ZALO_OTP_APP_ID=your_app_id
ZALO_OTP_APP_SECRET=your_app_secret
ZALO_OTP_BASE_URL=https://openapi.zalo.me/v2.0/
```

## Sử dụng

### Gửi OTP

```php
use TrungDV\ZaloOtp\Facades\ZaloOtp;

// Gửi OTP với message mặc định
$result = ZaloOtp::sendOtp('0123456789');

// Gửi OTP với message tùy chỉnh
$result = ZaloOtp::sendOtp('0123456789', 'Mã xác thực của bạn là: {otp_code}');
```

### Xác thực OTP

```php
$result = ZaloOtp::verifyOtp('0123456789', '123456');
```

### Lấy thông tin OTP

```php
$result = ZaloOtp::getOtpInfo('0123456789');
```

### Sử dụng Service trực tiếp

```php
use TrungDV\ZaloOtp\Services\ZaloOtpService;

$service = app(ZaloOtpService::class);
$result = $service->sendOtp('0123456789');
```

## Testing

```bash
composer test
```

## License

MIT

