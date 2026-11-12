<?php

namespace Codebrainbv\Smartpay\Http;

interface HttpPickerInterface
{
    /**
     * @param \GuzzleHttp\ClientInterface|\Codebrainbv\Smartpay\Http\HttpPicker $httpClient
     *
     * @return \Codebrainbv\Smartpay\Http\HttpPicker
     */
    public function pickHttpAdapter($httpClient);
}
