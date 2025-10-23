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
    protected ?string $template_id;
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
        $this->template_id = config('zalo-otp.template_id', '');
        $this->timeout = config('zalo-otp.timeout', 30);
        $this->retry_attempts = config('zalo-otp.retry_attempts', 3);
    }

    public function __call(string $method, array $arguments)
    {
        if (in_array($method, ['get', 'post', 'put', 'patch', 'option'])) {
            if (!empty($arguments[0])) {
                $endpoint = $arguments[0];
                if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
                    $url = $endpoint;
                } elseif (!empty($this->request_url)) {
                    if (str_starts_with($endpoint, '/')) {
                        $endpoint = substr($endpoint, 1);
                    }
                    $url = rtrim($this->request_url, '/') . '/' . $endpoint;
                } else {
                    $url = $endpoint;
                }
            } elseif (!empty($this->request_url)) {
                $url = $this->request_url;
            } else {
                throw new \InvalidArgumentException('No URL provided for ' . $method . ' request');
            }

            if (!empty($arguments[1])) {
                $params = is_array($arguments[1]) ? $arguments[1] : [$arguments[1]];
                $this->setParams($params);
            }

            if (!$this->params) {
                $this->response = $this->http()->{$method}($url);
            } else {
                $this->response = $this->http()->{$method}($url, $this->params);
            }

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
        if (!empty($this->params)) {
            // Check if Content-Type is form-urlencoded
            if (isset($this->headers['Content-Type']) &&
                str_contains($this->headers['Content-Type'], 'application/x-www-form-urlencoded')) {
                $http = $http->asForm();
            }
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
            'access_token' => $access_token
        ]);

        return $this;
    }

    public function setParams(array $params = [])
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function flushParams()
    {
        $this->params = [];

        return $this;
    }

    public function refreshToken()
    {
        $this->setRequestUrl(ZaloUri::ACCESS_TOKEN_URI, false)
            ->setHeaders([
                'secret_key' => $this->app_secret,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])
            ->setParams([
                "app_id" => $this->app_id,
                "grant_type" => ZaloUri::GRANT_TYPE_REFRESH_TOKEN,
                "refresh_token" => $this->refresh_token
            ]);
        $this->response = $this->post();

        return $this->response;
    }

    public function getStatusMessage(array $params = [])
    {
        $params = $this->formatParams($params);
        $url = ZaloUri::GET_STATUS_MESSAGE_URI . '?' . http_build_query($params);
        $this->flushParams();
        $this->setRequestUrl($url, true);
        $this->response = $this->get();

        return $this->response;
    }

    public function sendOtp( array $params = [])
    {
        $params = $this->formatParams($params);
        if (empty($params['template_id'])) {
            $params['template_id'] = $this->template_id;
        }
        $this->setRequestUrl(ZaloUri::SEND_OTP_URI)
            ->setParams($params);
        $this->response = $this->post();

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

    public function toCurlFromPendingRequest(PendingRequest $request, string $method, string $url, array $params = []): string
    {
        // Debug CURL
        $curl = "curl -X {$method} \"{$url}\" \\\n";

        // Headers
        foreach ($request->getOptions()['headers'] ?? [] as $key => $value) {
            $curl .= "  -H \"{$key}: {$value}\" \\\n";
        }
        // Body
        if (!empty($params)) {
            $contentType = $request->getOptions()['headers']['Content-Type'] ?? '';
            if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                $body = http_build_query($params);
            } else {
                $body = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $curl .= "  -d '" . $body . "'";
        }

        return $curl;
    }
}
