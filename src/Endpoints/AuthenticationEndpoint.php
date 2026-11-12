<?php

namespace Codebrainbv\Smartpay\Endpoints;

use Codebrainbv\Smartpay\Resource\Authentication;
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
     * Retreive an accesstoken from the Codebrain HUB.
     *
     * Will throw a ApiException if the shop cannot be found, or an accesstoken could not be generated.
     *
     * @return Authentication
     *
     * @throws ApiException
     */
    public function get(array $parameters = [])
    {
        return $this->get($parameters);
    }
}
