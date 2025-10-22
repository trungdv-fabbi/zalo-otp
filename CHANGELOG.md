# Changelog

All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this package adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Support for Laravel 12.x
- Method `formatPhoneNumber()` to format phone numbers
- Method `setRequestUrl()` with support for full URLs and endpoints
- Method `setAuthorization()` to set access token
- Method `setRefreshToken()` to set refresh token
- Method `getStatusMessage()` to get message status
- HTTP methods through `__call` magic method
- Singleton pattern with `getInstance()`

### Changed
- Improved error handling in `getJson()` method
- Optimized URL handling in `setRequestUrl()`
- Improved logic in `formatParams()` method

### Fixed
- Fixed URL duplicate issue when using `setRequestUrl()`
- Fixed logic error in `formatParams()` method
- Fixed type declaration for `$app_id` and `$app_secret`

## [1.0.0] - 2025-10-22

### Added
- Initial release
- Support for sending OTP via Zalo API
- Support for refreshing access token
- Support for getting message status
- `ZaloOtp` Facade for easy usage
- Service Provider for Laravel registration
- Configuration file with configurable options
- Support for HTTP methods (GET, POST, PUT, PATCH)
- Support for custom headers and parameters
- Error handling and logging
- Basic test cases
- Complete documentation

### Features
- **ZaloClient**: Main class for interacting with Zalo API
- **ZaloOtp Facade**: Facade for easy usage
- **ZaloOtpServiceProvider**: Service Provider for Laravel
- **Configuration**: Config file with environment variables
- **ZaloUri**: Constants for API endpoints
- **ErrorType**: Enum for error types

### Requirements
- PHP >= 8.1
- Laravel >= 9.0
- GuzzleHttp >= 7.0

### Installation
```bash
composer require trungdv/zalo-otp
php artisan vendor:publish --tag=config
```
