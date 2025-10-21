<?php

namespace TrungDV\ZaloOtp\Enums;

enum ErrorType: int
{
    case HTTP_OK = 200;
    case HTTP_BAD_REQUEST = 400;
    case HTTP_UNAUTHORIZED = 401;
    case HTTP_FORBIDDEN = 403;
    case HTTP_NOT_FOUND = 404;
    case HTTP_UNPROCESSABLE_ENTITY = 422;
    case HTTP_INTERNAL_SERVER_ERROR = 500;

    public function label(): string
    {
        return match($this) {
            self::HTTP_OK => __('HTTP_OK'),
            self::HTTP_BAD_REQUEST => __('error.MSG_INVALID_REQUEST'),
            self::HTTP_UNAUTHORIZED => __('error.MSG_NOT_AUTHORIZED'),
            self::HTTP_FORBIDDEN => __('error.MSG_CANNOT_ACCESS'),
            self::HTTP_NOT_FOUND => __('error.MSG_NOT_FOUND'),
            self::HTTP_UNPROCESSABLE_ENTITY => __('error.MSG_INVALID_REQUEST'),
            self::HTTP_INTERNAL_SERVER_ERROR => __('error.MSG_INTERNAL_ERROR'),
        };
    }
}
