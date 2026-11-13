<?php

namespace Codebrainbv\Smartpay\Resources;

use Codebrainbv\Smartpay\Resources\BaseResource;
use Codebrainbv\Smartpay\Enums\PaymentStates;
use Codebrainbv\Smartpay\Enums\PaymentMethods;
use Codebrainbv\Smartpay\SmartpayClient;

class Webhook extends BaseResource
{
    protected $client;

    public function __construct(SmartpayClient $client)
    {
        $this->client = $client;
    }
}
