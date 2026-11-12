<?php

namespace Codebrainbv\Smartpay\Http;

interface HttpInterface
{
    /**
     * Send a request to the specified Codebrain API endpoint.
     *
     * @param string       $httpMethod
     * @param string       $url
     * @param string|array $headers
     * @param string       $httpBody
     *
     * @return \stdClass|null
     *
     * @throws \Codebrainbv\Smartpay\Exceptions\ApiException
     */
    public function send($httpMethod, $url, $headers, $httpBody);

    /**
     * The version number for the underlying http client, if available.
     *
     * @example Guzzle/7.7
     *
     * @return string|null
     */
    public function versionString();
}
