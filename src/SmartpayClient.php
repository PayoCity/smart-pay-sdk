<?php

namespace Codebrainbv\Smartpay;

use Codebrainbv\Smartpay\Exceptions\ApiException;
use Codebrainbv\Smartpay\Http;
use Codebrainbv\Smartpay\Endpoints\AuthenticationEndpoint;
use Codebrainbv\Smartpay\Endpoints\OrderEndpoint;

class SmartpayClient
{
    /**
     * Version of our client.
     */
    public const CLIENT_VERSION = '0.0.1';

    /**
     * Version of the API.
     */
    public const API_VERSION = 'v2';


    /**
     * Poduction endpoint of the remote API.
     */
    public const API_PROD_ENDPOINT = 'https://api.pay.rabobank.nl';

    /**
     * Sandbox endpoint of the remote API.
     * @var string
     */
    public const API_SAND_ENDPOINT = 'https://api.pay-sandbox.rabobank.nl';

    /*
    * HTTP Methods
    */
    public const HTTP_GET = 'GET';
    public const HTTP_POST = 'POST';

    /**
     * Refresh token of the Rabo Smart Pay.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * Signing key for the Rabo Smart Pay.
     *
     * @var string
     */
    protected $signingKey;

    /**
     * Sandbox or production environment.
     *
     * @var string
     */
    protected $sandbox;


    /**
     * Access token for the Rabo Smart Pay, from cache or obtained via the refresh token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Production API endpoint.
     * @var string
     */
    protected $apiProdEndpoint = self::API_PROD_ENDPOINT;

    /**
     * Sandbox API endpoint.
     * @var string
     */
    protected $apiSandEndpoint = self::API_SAND_ENDPOINT;

    /**
     * @var \Codebrainbv\Smartpay\Http\HttpInterface
     */
    protected $httpClient;

    /**
     * RESTful Payments resource.
     *
     * @var AuthenticationEndpoint
     */
    public $auth;

    /**
     * RESTful Payments resource.
     *
     * @var OrderEndpoint
     */
    public $order;

    public function __construct($httpClient = null, $httpAdapterPicker = null)
    {
        $httpAdapterPicker = $httpAdapterPicker ?: new Http\HttpPicker();
        $this->httpClient = $httpAdapterPicker->pickHttpAdapter($httpClient);

        $this->initializeEndpoints();
    }

    public function initializeEndpoints()
    {
        $this->auth = new AuthenticationEndpoint($this);
        // $this->order = new OrderEndpoint($this);
    }

    /**
     * @param string $refreshToken the Point of Sale's refresh token
     *
     * @return SmartpayClient
     *
     * @throws \InvalidArgumentException
     */
    public function setRefreshToken($refreshToken)
    {
        $refreshToken = trim($refreshToken);

        // Refresh token has to be atleast 450 characters long.
        if (!preg_match('/^[a-zA-Z0-9#\/="]{450,}$/', $refreshToken)) {
            throw new \InvalidArgumentException(
                'Refresh token cannot be empty, or has to be longer then 450 characters.'
            );
        }

        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @param string $signingKey the Point of Sale's signing key
     *
     * @return SmartpayClient
     *
     * @throws \InvalidArgumentException
     */
    public function setSigningKey($signingKey)
    {
        $signingKey = trim($signingKey);

        // Signing key has to be atleast 40 characters long.
        if (!preg_match('/^[a-zA-Z0-9#\/="]{40,}$/', $signingKey)) {
            throw new \InvalidArgumentException(
                'Signing key cannot be empty, or has to be longer then 40 characters.'
            );
        }

        $this->signingKey = $signingKey;

        return $this;
    }

    /**
     * @param string $accessToken in cache (stored previously)
     *
     * @return SmartpayClient
     *
     * @throws \InvalidArgumentException
     */
    public function setAccessToken($accessToken)
    {
        $accessToken = trim($accessToken);

        // Check Accesstoken
        if (empty($accessToken)) {
            throw new \InvalidArgumentException(
                'Accesstoken cannot be empty, please use stored accesstoken or create a new one.'
            );
        }

        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Perform a http call. This method is used by the resource specific classes.
     *
     * @param string      $httpMethod
     * @param string      $apiPath
     * @param string|null $httpBody
     *
     * @return \stdClass
     *
     * @throws ApiException
     */
    public function doHttpCall($httpMethod, $apiPath, $httpBody = null)
    {
        $url = $this->getBaseUrl() . '/' . self::API_VERSION . '/' . $apiPath;

        return $this->performHttpCall($httpMethod, $url, $httpBody);
    }

    /**
     * Perform the auth http call. This method is used by the authentication resource class.
     *
     * @param string      $httpMethod
     * @param string      $apiPath
     * @param string|null $httpBody
     *
     * @return \stdClass
     *
     * @throws ApiException
     */
    public function doAuthHttpCall($httpMethod, $apiPath, $httpBody = null)
    {
        $url = $this->getBaseUrl() . '/' . self::API_VERSION . '/' . $apiPath;

        return $this->performAuthHttpCall($httpMethod, $url, $httpBody);
    }

    /**
     * Perform a http call to a full url. This method is used by the resource specific classes.
     *
     * @see $payments
     * @see $isuers
     *
     * @param string      $httpMethod
     * @param string      $url
     * @param string|null $httpBody
     *
     * @return \stdClass|null
     *
     * @throws ApiException
     */
    public function performHttpCall($httpMethod, $url, $httpBody = null)
    {
        if (empty($this->accessToken)) {
            throw new ApiException(
                'You have not set an accessToken. Please use setAccessToken() to set the access token in the client.'
            );
        }

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->accessToken}",
        ];

        if ($httpBody !== null) {
            $headers['Content-Type'] = 'application/json';
        }

        if (function_exists('php_uname')) {
            $headers['X-Codebrain-Client'] = php_uname();
        }

        $response = $this->httpClient->send($httpMethod, $url, $headers, $httpBody);

        // Check if x-signature headers exists and the hash is correct
        if (!isset($response->headers['x-signature'])) {
            throw new ApiException('Missing signature header.');
        } else {
            // Get the signature from the response, with correction for Guzzle 6
            if (is_array($response->headers['x-signature'])) {
                $signatureHeader = $response->headers['x-signature'][0];
            } else {
                $signatureHeader = $response->headers['x-signature'];
            }

            $signature = hash_hmac('sha512', json_encode($response->body), $this->signingKey);

            if (!hash_equals($signature, $signatureHeader)) {
                throw new ApiException('Invalid signature.');
            }
        }

        return $response->body;
    }

    /**
     * Perform a http call to the authentication url. This method is used by the resource specific classes.
     *
     * @see $payments
     * @see $isuers
     *
     * @param string      $httpMethod
     * @param string      $url
     * @param string|null $httpBody
     *
     * @return \stdClass|null
     *
     * @throws ApiException
     */
    public function performAuthHttpCall($httpMethod, $url, $httpBody = null)
    {
        if (empty($this->refreshToken)) {
            throw new ApiException(
                'You have not set a refresh token. Please use setRefreshToken() to set the refresh token in the client.'
            );
        }

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->refreshToken}",
        ];

        if (function_exists('php_uname')) {
            $headers['X-Codebrain-Client'] = php_uname();
        }

        $response = $this->httpClient->send($httpMethod, $url, $headers, $httpBody);

        return $response->body;
    }

    /**
     * Checks if the return is valid from the Codebrain HUB.
     *
     * @return bool
     *
     * @throws ApiException
     */
    public function validateReturn($data)
    {
    }

    /**
     * Checks if the webhook is valid from the Codebrain HUB.
     *
     * @param string $jsonData
     *
     * @return bool
     *
     * @throws ApiException
     */
    public function validateWebhook($jsonData)
    {
    }

    private function getBaseUrl(): string
    {
        if ($this->sandbox) {
            return self::API_SAND_ENDPOINT;
        }

        return self::API_PROD_ENDPOINT;
    }

    private function isUuid($string)
    {
        if (!is_string($string)) {
            return false;
        }

        return preg_match('/^[\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}$/D', $string) > 0;
    }
}
