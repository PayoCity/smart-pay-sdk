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


    public function get(array $params)
    {
        if (empty($params['authentication'])) {
            throw new ApiException("Missing authentication token in webhook data.");
        }

        return $this->restWebhook($params['authentication']);
    }
}
