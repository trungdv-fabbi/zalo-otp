<?php

namespace TrungDV\ZaloOtp\Tests;

use PHPUnit\Framework\TestCase;
use TrungDV\ZaloOtp\Services\ZaloOtpService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;

class ZaloOtpServiceTest extends TestCase
{
    protected ZaloOtpService $service;
    protected $mockClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock GuzzleHttp Client
        $this->mockClient = Mockery::mock(Client::class);
        $this->service = new ZaloOtpService();
        
        // Sử dụng reflection để inject mock client
        $reflection = new \ReflectionClass($this->service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->service, $this->mockClient);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_otp_success()
    {
        $phoneNumber = '0123456789';
        $message = 'Mã OTP của bạn là: 123456';
        
        $mockResponse = new Response(200, [], json_encode([
            'success' => true,
            'data' => ['otp_id' => 'test_otp_id']
        ]));

        $this->mockClient
            ->shouldReceive('post')
            ->once()
            ->with(
                'https://openapi.zalo.me/v2.0/otp/send',
                Mockery::type('array')
            )
            ->andReturn($mockResponse);

        $result = $this->service->sendOtp($phoneNumber, $message);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_send_otp_failure()
    {
        $phoneNumber = '0123456789';
        
        $this->mockClient
            ->shouldReceive('post')
            ->once()
            ->andThrow(new \GuzzleHttp\Exception\RequestException(
                'Request failed',
                new \GuzzleHttp\Psr7\Request('POST', 'test')
            ));

        $result = $this->service->sendOtp($phoneNumber);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_verify_otp_success()
    {
        $phoneNumber = '0123456789';
        $otpCode = '123456';
        
        $mockResponse = new Response(200, [], json_encode([
            'success' => true,
            'verified' => true
        ]));

        $this->mockClient
            ->shouldReceive('post')
            ->once()
            ->with(
                'https://openapi.zalo.me/v2.0/otp/verify',
                Mockery::type('array')
            )
            ->andReturn($mockResponse);

        $result = $this->service->verifyOtp($phoneNumber, $otpCode);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_verify_otp_failure()
    {
        $phoneNumber = '0123456789';
        $otpCode = 'wrong_code';
        
        $this->mockClient
            ->shouldReceive('post')
            ->once()
            ->andThrow(new \GuzzleHttp\Exception\RequestException(
                'Request failed',
                new \GuzzleHttp\Psr7\Request('POST', 'test')
            ));

        $result = $this->service->verifyOtp($phoneNumber, $otpCode);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_get_otp_info_success()
    {
        $phoneNumber = '0123456789';
        
        $mockResponse = new Response(200, [], json_encode([
            'success' => true,
            'data' => ['status' => 'sent']
        ]));

        $this->mockClient
            ->shouldReceive('get')
            ->once()
            ->with(
                'https://openapi.zalo.me/v2.0/otp/info',
                Mockery::type('array')
            )
            ->andReturn($mockResponse);

        $result = $this->service->getOtpInfo($phoneNumber);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }
}

