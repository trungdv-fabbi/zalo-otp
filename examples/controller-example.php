<?php

namespace App\Http\Controllers;

use TrungDV\ZaloOtp\Facades\ZaloOtp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Example Controller showing how to use ZaloOtp package
 */
class ZaloOtpController extends Controller
{
    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'message' => 'nullable|string|max:160'
        ]);

        try {
            $client = ZaloOtp::getInstance();
            $response = $client->sendOtp([
                'phone' => $request->phone,
                'message' => $request->message ?? 'Mã OTP của bạn là: {otp_code}'
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP',
                    'error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Zalo OTP Send Error', [
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'otp_code' => 'required|string|size:6'
        ]);

        try {
            $client = ZaloOtp::getInstance();
            $response = $client->verifyOtp($request->phone, $request->otp_code);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP code',
                    'error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Zalo OTP Verify Error', [
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get OTP information
     */
    public function getOtpInfo(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/'
        ]);

        try {
            $client = ZaloOtp::getInstance();
            $response = $client->getOtpInfo($request->phone);

            return response()->json([
                'success' => $response->successful(),
                'data' => $response->json()
            ]);
        } catch (\Exception $e) {
            Log::error('Zalo OTP Info Error', [
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Refresh access token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        try {
            $client = ZaloOtp::getInstance();
            $client->setRefreshToken($request->refresh_token);
            $response = $client->refreshToken();

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token refreshed successfully',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to refresh token',
                    'error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Zalo OTP Refresh Token Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get message status
     */
    public function getMessageStatus(Request $request): JsonResponse
    {
        $request->validate([
            'message_id' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/'
        ]);

        try {
            $client = ZaloOtp::getInstance();
            $response = $client->getStatusMessage([
                'message_id' => $request->message_id,
                'phone' => $request->phone
            ]);

            return response()->json([
                'success' => $response->successful(),
                'data' => $response->json()
            ]);
        } catch (\Exception $e) {
            Log::error('Zalo OTP Message Status Error', [
                'message_id' => $request->message_id,
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
