<?php

namespace Codebrainbv\Smartpay\Http;

use Codebrainbv\Smartpay\Exceptions\UnknownClientException;
use Codebrainbv\Smartpay\Http\HttpInterface;

class HttpPicker implements HttpPickerInterface
{
    /**
     * @param \GuzzleHttp\ClientInterface|HttpInterface|\stdClass|null $httpClient
     *
     * @return HttpInterface
     *
     * @throws UnknownClientException
     */
    public function pickHttpAdapter($httpClient)
    {
        if (!$httpClient) {
            if ($this->guzzleIsDetected()) {
                $guzzleVersion = $this->guzzleMajorVersionNumber();

                if ($guzzleVersion && in_array($guzzleVersion, [7])) {
                    return Guzzle7Http::createDefault();
                }
            }

            return new CurlHttp();
        }

        if ($httpClient instanceof HttpInterface) {
            return $httpClient;
        }

        if ($httpClient instanceof \GuzzleHttp\ClientInterface) {
            return new Guzzle7Http($httpClient);
        }

        throw new UnknownClientException('The provided http client or adapter was not recognized.');
    }

    /**
     * @return bool
     */
    private function guzzleIsDetected()
    {
        return interface_exists('\\' . \GuzzleHttp\ClientInterface::class);
    }

    /**
     * @return int|null
     */
    private function guzzleMajorVersionNumber()
    {
        // Guzzle 7
        if (defined('\GuzzleHttp\ClientInterface::MAJOR_VERSION')) {
            return (int) \GuzzleHttp\ClientInterface::MAJOR_VERSION;
        }

        return null;
    }
}
