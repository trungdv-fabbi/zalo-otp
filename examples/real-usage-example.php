<?php

/**
 * Real Usage Example for Zalo OTP Package
 *
 * This example shows how to use the package in a real Laravel application
 * with proper error handling and response processing.
 */

use TrungDV\ZaloOtp\Facades\ZaloOtp;
use Illuminate\Support\Facades\Log;

class ZaloOtpService
{
    protected $client;

    public function __construct()
    {
        $this->client = ZaloOtp::getInstance();
    }

    /**
     * Send OTP to phone number
     */
    public function sendOtp(string $phone, string $otp, string $templateId = null): array
    {
        try {
            // Set business URL for OTP sending
            $this->client->setRequestUrl('message/template', true);

            // Set authorization header
            $this->client->setAuthorization(config('zalo-otp.access_token'));

            // Send OTP
            $response = $this->client->sendOtp([
                'phone' => $this->formatPhoneNumber($phone),
                'template_id' => $templateId ?? config('zalo-otp.default_template_id'),
                'template_data' => [
                    'otp' => $otp
                ],
                'mode' => config('app.env') !== 'production' ? 'development' : 'production',
                'tracking_id' => uniqid('otp_', true)
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('OTP sent successfully', [
                    'phone' => $phone,
                    'message_id' => $data['message_id'] ?? null,
                    'tracking_id' => $data['tracking_id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'data' => $data
                ];
            } else {
                $error = $response->json();

                Log::error('Failed to send OTP', [
                    'phone' => $phone,
                    'error' => $error
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send OTP',
                    'error' => $error
                ];
            }
        } catch (\Exception $e) {
            Log::error('OTP send exception', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get message status
     */
    public function getMessageStatus(string $messageId, string $phone): array
    {
        try {
            $this->client->setRequestUrl('message/status', true);
            $this->client->setAuthorization(config('zalo-otp.access_token'));

            $response = $this->client->getStatusMessage([
                'message_id' => $messageId,
                'phone' => $this->formatPhoneNumber($phone)
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Get message status exception', [
                'message_id' => $messageId,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        try {
            $this->client->setRequestUrl('oa/access_token', false);
            $this->client->setRefreshToken($refreshToken);

            $response = $this->client->refreshToken();

            if ($response->successful()) {
                $data = $response->json();

                // Update config with new tokens
                config(['zalo-otp.access_token' => $data['access_token']]);
                config(['zalo-otp.refresh_token' => $data['refresh_token']]);

                Log::info('Access token refreshed successfully');

                return [
                    'success' => true,
                    'message' => 'Token refreshed successfully',
                    'data' => $data
                ];
            } else {
                $error = $response->json();

                Log::error('Failed to refresh token', ['error' => $error]);

                return [
                    'success' => false,
                    'message' => 'Failed to refresh token',
                    'error' => $error
                ];
            }
        } catch (\Exception $e) {
            Log::error('Refresh token exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number (convert 0 to 84)
     */
    private function formatPhoneNumber(string $phone): string
    {
        return $this->client->formatPhoneNumber($phone);
    }
}

// Usage in Controller
class ZaloOtpController extends Controller
{
    protected $zaloOtpService;

    public function __construct(ZaloOtpService $zaloOtpService)
    {
        $this->zaloOtpService = $zaloOtpService;
    }

    /**
     * Send OTP endpoint
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'template_id' => 'nullable|string',
        ]);

        // Generate OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Send OTP
        $result = $this->zaloOtpService->sendOtp(
            $request->phone,
            $otp,
            $request->template_id
        );

        // Store OTP in cache for verification
        if ($result['success']) {
            Cache::put("otp_{$request->phone}", $otp, 300); // 5 minutes
        }

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Verify OTP endpoint
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'otp' => 'required|string|size:6'
        ]);

        $cachedOtp = Cache::get("otp_{$request->phone}");

        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or not found'
            ], 400);
        }

        if ($cachedOtp !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code'
            ], 400);
        }

        // Clear OTP from cache
        Cache::forget("otp_{$request->phone}");

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    /**
     * Get message status endpoint
     */
    public function getMessageStatus(Request $request)
    {
        $request->validate([
            'message_id' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/'
        ]);

        $result = $this->zaloOtpService->getMessageStatus(
            $request->message_id,
            $request->phone
        );

        return response()->json($result);
    }

    /**
     * Refresh token endpoint
     */
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        $result = $this->zaloOtpService->refreshAccessToken($request->refresh_token);

        return response()->json($result);
    }
}
