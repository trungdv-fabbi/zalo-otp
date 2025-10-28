<?php

declare(strict_types=1);

namespace TrungDV\ZaloOtp\Exceptions;

use TrungDV\ZaloOtp\Consts\ZaloErrorCode;

class ZaloException extends \RuntimeException
{
    protected int $zaloErrorCode;
    protected ?string $zaloErrorDetail;

    public function __construct(
        int $zaloErrorCode,
        ?string $zaloErrorDetail = null,
        ?string $message = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $this->zaloErrorCode = $zaloErrorCode;
        $this->zaloErrorDetail = $zaloErrorDetail;

        $message = $message ?? ZaloErrorCode::getMessage($zaloErrorCode);

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get Zalo error code
     */
    public function getZaloErrorCode(): int
    {
        return $this->zaloErrorCode;
    }

    /**
     * Get Zalo error detail from Zalo API
     */
    public function getZaloErrorDetail(): ?string
    {
        return $this->zaloErrorDetail;
    }

    /**
     * Check if Zalo request is successful
     */
    public function isSuccess(): bool
    {
        return ZaloErrorCode::isSuccess($this->zaloErrorCode);
    }

    /**
     * Create exception from Zalo API response
     */
    public static function fromResponse(array $response): self
    {
        $errorCode = $response['error'] ?? ZaloErrorCode::UNKNOWN_ERROR;
        $errorMessage = $response['message'] ?? null;

        return new self(
            zaloErrorCode: (int) $errorCode,
            zaloErrorDetail: $errorMessage,
            message: ZaloErrorCode::getMessage((int) $errorCode)
        );
    }

    /**
     * Get exception data as array
     */
    public function toArray(): array
    {
        return [
            'zalo_error_code' => $this->zaloErrorCode,
            'zalo_error_detail' => $this->zaloErrorDetail,
            'message' => $this->getMessage(),
        ];
    }
}


