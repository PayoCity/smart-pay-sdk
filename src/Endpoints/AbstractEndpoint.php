<?php

namespace Codebrainbv\Smartpay\Endpoints;

use Codebrainbv\Smartpay\SmartpayClient;
use Codebrainbv\Smartpay\Exceptions\ApiException;
use Codebrainbv\Smartpay\Resources\ResourceFactory;
use Codebrainbv\Smartpay\Resources\BaseResource;

abstract class AbstractEndpoint
{
    public const REST_CREATE = SmartpayClient::HTTP_POST;
    public const REST_READ = SmartpayClient::HTTP_GET;

    /**
     * @var SmartpayClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $resourcePath;

    /**
     * @var string|null
     */
    protected $parentId;

    public function __construct(SmartpayClient $api)
    {
        $this->client = $api;
    }

    protected function gatekeeper()
    {
        $result = $this->client->doAuthHttpCall(
            self::REST_READ,
            "{$this->getResourcePath()}"
        );

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }


    /**
     * @return string
     *
     * @throws ApiException
     */
    public function getResourcePath()
    {
        if (strpos($this->resourcePath, '_') !== false) {
            [$parentResource, $childResource] = explode('_', $this->resourcePath, 2);

            if (empty($this->parentId)) {
                throw new ApiException("Subresource '{$this->resourcePath}' used without parent '$parentResource' ID.");
            }

            return "$parentResource/{$this->parentId}/$childResource";
        }

        return $this->resourcePath;
    }

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return BaseResource
     */
    abstract protected function getResourceObject();
}
