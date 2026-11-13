<?php

namespace Codebrainbv\Smartpay\Endpoints;

use Codebrainbv\Smartpay\Endpoints\AbstractEndpoint;
use Codebrainbv\Smartpay\Exceptions\ApiException;
use Codebrainbv\Smartpay\Resources\Status;

class StatusEndpoint extends AbstractEndpoint
{
    protected $resourcePath = 'v2/orders';

    /**
     * @return Status
     */
    protected function getResourceObject()
    {
        return new Status($this->client);
    }


    public function get(string $omnikassaOrderId)
    {
        if (empty($omnikassaOrderId)) {
            throw new ApiException("Missing order ID: '{$omnikassaOrderId}'.");
        }

        return $this->restRead($omnikassaOrderId);
    }
}
