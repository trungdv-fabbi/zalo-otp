<?php

declare(strict_types=1);

namespace TrungDV\ZaloOtp;

class ZaloUri
{
    // OAuth endpoints
    public const ACCESS_TOKEN_URI = 'oa/access_token';
    public const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';

    // Business API endpoints
    public const SEND_OTP_URI = 'message/template';
    public const GET_STATUS_MESSAGE_URI = 'message/status';
}
