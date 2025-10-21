<?php

declare(strict_types=1);

namespace TrungDV\ZaloOtp;

class ZaloUri
{
    public const ACCESS_TOKEN_URI = 'oauth/access_token';
    public const GET_STATUS_MESSAGE_URI = 'message/status';
    public const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';
    public const SEND_OTP_URI = 'message/template';
}
