<?php

namespace Codebrainbv\Smartpay\Endpoints;

use Codebrainbv\Smartpay\Resource\OrderResource;
use Codebrainbv\Smartpay\Exception\ApiException;
use Codebrainbv\Smartpay\Endpoints\AbstractEndpoint;

class OrderEndpoint extends AbstractEndpoint
{
    // Payments
    protected $resourcePath = 'omnikassa-api/order/server/api/v2/order';
}
