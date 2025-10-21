<?php

declare(strict_types=1);

namespace TrungDV\ZaloOtp;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use TrungDV\ZaloOtp\Enums\ErrorType;

class ZaloClient
{
    public static $instance;

    protected string $base_url;
    protected string $bussiness_base_url;
    protected ?string $app_id;
    protected ?string $app_secret;
    protected int $timeout;
    protected int $retry_attempts;
    protected string $access_token = '';
    protected string $refresh_token = '';
    protected string $code = '';
    protected string $request_url = '';
    protected array $headers = [
        'Content-Type' => 'application/json; charset=utf-8',
    ];
    protected array $params = [];

    public int $status = 200;
    public mixed $response = null;

    public function __construct()
    {
        $this->base_url = config('zalo-otp.base_url', 'https://oauth.zaloapp.com/v4/');
        $this->bussiness_base_url = config('zalo-otp.bussiness_base_url', 'https://business.openapi.zalo.me/');
        $this->app_id = config('zalo-otp.app_id', '');
        $this->app_secret = config('zalo-otp.app_secret', '');
        $this->timeout = config('zalo-otp.timeout', 30);
        $this->retry_attempts = config('zalo-otp.retry_attempts', 3);
    }

    public function __call(string $method, array $arguments)
    {
        if (in_array($method, ['get', 'post', 'put', 'patch', 'option']) && !empty($arguments)) {
            if (!empty($this->request_url)) {
                $endpoint = $arguments[0];
                if (str_starts_with($endpoint, '/')) {
                    $endpoint = substr($endpoint, 1);
                }
                $url = rtrim($this->request_url, '/') . '/' . $endpoint;
            } else {
                $url = $arguments[0];
            }

            if (!empty($arguments[1])) {
                $params = is_array($arguments[1]) ? $arguments[1] : [$arguments[1]];
                $this->setParams($params);
            }
            $this->response = $this->http()->{$method}($url);

            return $this->response;
        }
    }

    public static function getInstance()
    {
        $http = self::$instance;
        if (!$http) {
            $http = new self();
        }

        return $http;
    }

    public function http(): PendingRequest
    {
        $http = Http::withHeaders($this->headers);
        if(!empty($this->params)) {
            $http = $http->withBody(json_encode($this->params));
        }
        return $http;
    }

    public function setRequestUrl(string $uri = '', bool $isBusiness = true)
    {
        if (empty($uri)) {
            $this->request_url = $isBusiness ? $this->bussiness_base_url : $this->base_url;
        } else {
            if (str_starts_with($uri, 'http')) {
                $this->request_url = $uri;
            } else {
                $baseUrl = $isBusiness ? $this->bussiness_base_url : $this->base_url;
                $this->request_url = rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
            }
        }
        return $this;
    }

    public function getUrl()
    {
        return $this->request_url;
    }

    public function setCode(string $code = '')
    {
        $this->code = $code;
        return $this;
    }
    public function setRefreshToken(string $refresh_token = '')
    {
        $this->refresh_token = $refresh_token;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function setAuthorization(string $access_token)
    {
        $this->setHeaders([
            'access_token' => "{$access_token}"
        ]);

        return $this;
    }

    public function setParams(array $params = [])
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function refreshToken()
    {
        $this->setRequestUrl(ZaloUri::ACCESS_TOKEN_URI, false)
            ->setHeaders(['secret_key' => $this->app_secret])
            ->setParams([
                "app_id" => $this->app_id,
                "grant_type" => ZaloUri::GRANT_TYPE_REFRESH_TOKEN,
                "refresh_token" => $this->refresh_token
            ]);
        $this->response = $this->http()
            ->post($this->request_url);
        return $this->response;
    }

    public function getStatusMessage(array $params = [])
    {
        $params = $this->formatParams($params);
        $queryParams = http_build_query($params);
        $url = $this->setRequestUrl(ZaloUri::GET_STATUS_MESSAGE_URI, false)->getUrl();
        $this->response = $this->http()
            ->get($url . '?' . $queryParams);
        return $this->response;
    }

    public function sendOtp( array $params = [])
    {
        $params = $this->formatParams($params);
        $this->setRequestUrl(ZaloUri::SEND_OTP_URI)
            ->setParams($params);
        $this->response = $this->http()
            ->post($this->request_url);
        return $this->response;
    }

    public function formatParams(array $params = [])
    {
        if (!empty($params['phone'])) {
            $params['phone'] = $this->formatPhoneNumber($params['phone']);
        }

        return $params;
    }

    public function formatPhoneNumber(string $phoneNumber = '')
    {
        return preg_replace('/^0/', '84', $phoneNumber);
    }
}
