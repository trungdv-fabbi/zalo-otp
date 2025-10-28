<?php

declare(strict_types=1);

namespace TrungDV\ZaloOtp\Consts;

enum ZaloErrorCode: int
{
    case SUCCESS = 0;
    case UNKNOWN_ERROR = -100;
    case APPLICATION_INVALID = -101;
    case APPLICATION_NOT_EXISTED = -102;
    case APPLICATION_NOT_ACTIVATED = -103;
    case APP_SECRET_KEY_INVALID = -104;
    case APPLICATION_NOT_LINK_TO_OA = -105;
    case METHOD_UNSUPPORTED = -106;
    case MESSAGE_ID_INVALID = -107;
    case PHONE_NUMBER_INVALID = -108;
    public const UNKNOWN_ERROR = -100;
    case TEMPLATE_ID_INVALID = -109;
    case CAN_NOT_EDIT_TEMPLATE = -1091;
    case ZALO_VERSION_UNSUPPORTED = -110;
    public const TEMPLATE_DATA_EMPTY = -111;
    public const TEMPLATE_DATA_TYPE_NOT_DEFINE = -112;
    public const PARAMETER_BREAKS_MAX_LENGTH = -1121;
    public const TEMPLATE_DATA_MISSING_PARAMETER = -1122;
    public const QR_CODE_CANNOT_GENERATED = -1123;
    public const PARAMETER_INVALID_FORMAT = -1124;
    public const BUTTON_INVALID = -113;
    public const INVALID_BUTTON_CONTENT_FORMAT = -1131;
    public const USER_INACTIVE = -114;
    public const OUT_OF_QUOTA = -115;
    public const TEXT_INVALID = -116;
    public const NO_PERMISSION_TEMPLATE = -117;
    public const ZALO_ACCOUNT_NOT_EXISTED = -118;

    // Account & Permission errors (-119 to -135)
    public const ACCOUNT_CANNOT_RECEIVE_MESSAGE = -119;
    public const OA_NO_PERMISSION = -120;
    public const OA_NO_PERMISSION_CREATE_TEMPLATE = -1201;
    public const OA_NO_PERMISSION_USE_RESOURCE = -1202;
    public const BODY_DATA_EMPTY = -121;
    public const BODY_FORMAT_INVALID = -122;
    public const RSA_MESSAGE_DECODED_FAILED = -123;
    public const ACCESS_TOKEN_INVALID = -124;
    public const INVALID_APPSECRET_PROOF = -1241;
    public const OFFICIAL_ACCOUNT_ID_INVALID = -125;
    public const OUT_OF_QUOTA_DEV_MODE = -126;
    public const TEST_TEMPLATE_ADMIN_ONLY = -127;
    public const ENCODING_KEY_NOT_EXISTED = -128;
    public const RSA_KEY_CANNOT_GENERATED = -129;
    public const MAXIMUM_CHARACTER_LIMIT_EXCEEDED = -130;
    public const ZNS_TEMPLATE_NOT_APPROVED = -131;
    public const PARAMETER_INVALID = -132;
    public const CANNOT_SEND_AT_NIGHT = -133;
    public const USER_NOT_RESPONDED_OPT_IN = -134;
    public const NO_PERMISSION_SEND_ZNS = -135;
    public const OA_BLOCKED_DUE_VIOLATION = -1351;

    // ZCA & Feature errors (-136 to -153)
    public const ZCA_ASSOCIATION_REQUIRED = -136;
    public const ZCA_CHARGE_FAILURE = -137;
    public const APP_NO_PERMISSION_FEATURE = -138;
    public const EXTENSION_NO_PERMISSION_ZCA = -1381;
    public const USER_REFUSED_ZNS_TYPE = -139;
    public const USER_NOT_ELIGIBLE = -140;
    public const USER_REFUSED_ZNS = -141;
    public const RSA_KEY_NOT_EXIST = -142;
    public const RSA_KEY_ALREADY_EXISTED = -143;
    public const ZNS_DAILY_QUOTA_EXCEEDED = -144;
    public const OA_MONTHLY_PROMOTION_QUOTA_EXCEEDED = -1441;
    public const OA_NO_PERMISSION_ZNS_TYPE = -145;
    public const TEMPLATE_DISABLED_LOW_QUALITY = -146;
    public const TEMPLATE_DAILY_QUOTA_EXCEEDED = -147;
    public const OA_EXCEEDED_MONTHLY_FOLLOWUP = -1471;
    public const ZNS_JOURNEY_TOKEN_MISSING = -148;
    public const ZNS_JOURNEY_TOKEN_INVALID = -149;
    public const ZNS_JOURNEY_TOKEN_TYPE_INVALID = -1491;
    public const ZNS_JOURNEY_TOKEN_EXPIRED = -150;
    public const NOT_E2EE_TEMPLATE = -151;
    public const GET_E2EE_KEY_FAILED = -152;
    public const DATA_INVALID = -153;

    // Error messages mapping
    public const ERROR_MESSAGES = [
        self::SUCCESS => 'Gửi thành công',
        self::UNKNOWN_ERROR => 'Xảy ra lỗi không xác định, vui lòng thử lại sau',
        self::APPLICATION_NOT_ACTIVATED => 'Ứng dụng chưa được kích hoạt',
        self::PHONE_NUMBER_INVALID => 'Số điện thoại không hợp lệ',
        self::ZALO_VERSION_UNSUPPORTED => 'Phiên bản Zalo app không được hỗ trợ. Người dùng cần cập nhật phiên bản mới nhất',
        self::PARAMETER_BREAKS_MAX_LENGTH => 'Dữ liệu tham số vượt quá giới hạn ký tự',
        self::PARAMETER_INVALID_FORMAT => 'Dữ liệu tham số không đúng format',
        self::USER_INACTIVE => 'Người dùng không nhận được tin nhắn vì các lý do: Trạng thái tài khoản, Tùy chọn nhận ZNS, Sử dụng Zalo phiên bản cũ, hoặc các lỗi nội bộ khác',
        self::OUT_OF_QUOTA => 'Tài khoản ZNS không đủ số dư',
        self::ZNS_DAILY_QUOTA_EXCEEDED => 'OA đã vượt giới hạn gửi ZNS trong ngày',
        self::OA_MONTHLY_PROMOTION_QUOTA_EXCEEDED => 'OA request gửi vượt ngưỡng monthly promotion quota',
    ];

    /**
     * Get error message by code
     */
    public static function getMessage(int $code): string
    {
        return self::ERROR_MESSAGES[$code] ?? 'Unknown error';
    }

    /**
     * Check if error code is success
     */
    public static function isSuccess(int $code): bool
    {
        return $code === self::SUCCESS;
    }
}

