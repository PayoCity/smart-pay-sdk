<?php

namespace Codebrainbv\Smartpay\Endpoints;

use Codebrainbv\Smartpay\Resources\Authentication;
use Codebrainbv\Smartpay\Exceptions\ApiException;

class AuthenticationEndpoint extends AbstractEndpoint
{
    protected $resourcePath = 'omnikassa-api/gatekeeper/refresh';


    /**
     * @return Authentication
     */
    protected function getResourceObject()
    {
        return new Authentication($this->client);
    }

    /**
     * Retrieve an accesstoken from the Rabo Smart Pay.
     *
     * Will throw a ApiException if  an accesstoken could not be generated.
     *
     * @return Authentication
     *
     * @throws ApiException
     */
    public function get()
    {
        return $this->gatekeeper();
    }
}
