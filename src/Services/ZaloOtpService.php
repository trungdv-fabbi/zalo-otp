<?php

namespace TrungDV\ZaloOtp\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ZaloOtpService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $appId;
    protected string $appSecret;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('zalo-otp.base_url', 'https://openapi.zalo.me/v2.0/');
        $this->appId = config('zalo-otp.app_id');
        $this->appSecret = config('zalo-otp.app_secret');
    }

    /**
     * Gửi OTP qua Zalo
     */
    public function sendOtp(string $phoneNumber, string $message = null): array
    {
        try {
            $message = $message ?? 'Mã OTP của bạn là: {otp_code}';
            
            $response = $this->client->post($this->baseUrl . 'otp/send', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'app_id' => $this->appId,
                    'app_secret' => $this->appSecret,
                    'phone' => $phoneNumber,
                    'message' => $message,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            Log::info('Zalo OTP sent successfully', [
                'phone' => $phoneNumber,
                'response' => $data
            ]);

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (GuzzleException $e) {
            Log::error('Zalo OTP send failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Xác thực OTP
     */
    public function verifyOtp(string $phoneNumber, string $otpCode): array
    {
        try {
            $response = $this->client->post($this->baseUrl . 'otp/verify', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'app_id' => $this->appId,
                    'app_secret' => $this->appSecret,
                    'phone' => $phoneNumber,
                    'otp_code' => $otpCode,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            Log::info('Zalo OTP verified', [
                'phone' => $phoneNumber,
                'success' => $data['success'] ?? false
            ]);

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (GuzzleException $e) {
            Log::error('Zalo OTP verification failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Lấy thông tin OTP
     */
    public function getOtpInfo(string $phoneNumber): array
    {
        try {
            $response = $this->client->get($this->baseUrl . 'otp/info', [
                'query' => [
                    'app_id' => $this->appId,
                    'app_secret' => $this->appSecret,
                    'phone' => $phoneNumber,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (GuzzleException $e) {
            Log::error('Zalo OTP info failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

