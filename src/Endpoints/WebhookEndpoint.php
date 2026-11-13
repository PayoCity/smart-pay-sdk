<?php

namespace Codebrainbv\Smartpay\Endpoints;

use Codebrainbv\Smartpay\Endpoints\AbstractEndpoint;
use Codebrainbv\Smartpay\Resources\Webhook;
use Codebrainbv\Smartpay\Exceptions\ApiException;

class WebhookEndpoint extends AbstractEndpoint
{
    protected $resourcePath = 'omnikassa-api/order/server/api/v2/events/results/merchant.order.status.changed';

    /**
     * @return Webhook
     */
    protected function getResourceObject()
    {
        return new Webhook($this->client);
    }


    public function get(string $omnikassaOrderId)
    {
        if (empty($omnikassaOrderId)) {
            throw new ApiException("Missing order ID: '{$omnikassaOrderId}'.");
        }

        return $this->restRead($omnikassaOrderId);
    }
}
