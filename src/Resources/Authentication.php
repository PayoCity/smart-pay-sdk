<?php

namespace Codebrainbv\Smartpay\Resources;

use Codebrainbv\Smartpay\Resources\BaseResource;

class Authentication extends BaseResource
{
    /**
     * The access token that authorizes the webshop to perform other API calls for a limited amount of time.
     * After expiration a new access token must be retrieved.
     *
     * @var string
     */
    public $token;

    /**
     * The timestamp indicating when the access token expires.
     *
     * @var string
     */
    public $validUntil;

    /**
     * The duration in milliseconds for which the access token is valid.
     *
     * @var int
     */
    public $durationInMillis;

    protected static $allowedProperties = [
        'token',
        'validUntil',
        'durationInMillis'
    ];

    /**
     * Get the accesstoken.
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        if (empty($this->token)) {
            return null;
            // throw new \RuntimeException('Access token is not available.');
        }

        return $this->token;
    }

    /**
     * Get the duration in milliseconds for which the access token is valid.
     *
     * @return int|null
     */
    public function getDurationInMillis()
    {
        if (empty($this->durationInMillis)) {
            return null;
        }

        return $this->durationInMillis;
    }

    /**
     * Get the timestamp indicating when the access token expires.
     *
     * @return string|null
     */
    public function getValidUntil()
    {
        if (empty($this->validUntil)) {
            return null;
        }

        return $this->validUntil;
    }
}
