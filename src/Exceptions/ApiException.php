<?php

namespace Codebrainbv\Smartpay\Exceptions;

class ApiException extends \Exception
{
    /**
     * @var string|null
     */
    protected $plainMessage;

    /**
     * @var \Psr\Http\Message\RequestInterface|null
     */
    protected $request;

    /**
     * @var \Psr\Http\Message\ResponseInterface|null
     */
    protected $response;

    /**
     * API error code from the response
     *
     * @var string|int|null
     */
    protected $apiErrorCode;

    /**
     * API error message from the response
     *
     * @var string|null
     */
    protected $apiErrorMessage;

    /**
     * ISO8601 representation of the moment this exception was thrown.
     *
     * @var \DateTimeImmutable
     */
    protected $raisedAt;

    /**
     * @param string                                   $message
     * @param int                                      $code
     * @param string|null                              $field
     * @param string|int|null                          $error
     * @param \Psr\Http\Message\RequestInterface|null  $request
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @param \Throwable|null                          $previous
     *
     * @throws \Codebrainbv\Smartpay\Exceptions\ApiException
     */
    public function __construct(
        $message = '',
        $code = 0,
        $field = null,
        $error = null,
        $request = null,
        $response = null,
        $previous = null
    ) {
        $this->plainMessage = $message;
        $this->apiErrorCode = $error;
        $this->raisedAt = new \DateTimeImmutable();

        $formattedRaisedAt = $this->raisedAt->format(\DateTime::ISO8601_EXPANDED);
        $message = "[{$formattedRaisedAt}] " . $message;

        if (!empty($response)) {
            $this->response = $response;
        }

        $this->request = $request;
        if ($request) {
            $requestBody = $request->getBody()->__toString();

            if ($requestBody) {
                $message .= ". Request body: {$requestBody}";
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Throwable|null                     $previous
     *
     * @return \Codebrainbv\Smartpay\Exceptions\ApiException
     *
     * @throws \Codebrainbv\Smartpay\Exceptions\ApiException
     */
    public static function createFromResponse($response, $request = null, $previous = null)
    {
        $object = static::parseResponseBody($response);

        $errorCode = null;
        $errorMessage = null;
        if (!empty($object->errorCode)) {
            $errorCode = $object->errorCode;
            $errorMessage = $object->errorMessage;
        }

        $exception = new self(
            "Error executing API call ({$errorCode}: {$errorMessage})",
            $response->getStatusCode(),
            $errorCode,
            $errorMessage,
            $request,
            $response,
            $previous
        );

        $exception->apiErrorCode = $errorCode;
        $exception->apiErrorMessage = $errorMessage;

        return $exception;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the ISO8601 representation of the moment this exception was thrown.
     *
     * @return \DateTimeImmutable
     */
    public function getRaisedAt()
    {
        return $this->raisedAt;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \stdClass
     *
     * @throws \Codebrainbv\Smartpay\Exceptions\ApiException
     */
    protected static function parseResponseBody($response)
    {
        $body = (string) $response->getBody();

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Codebrainbv\Smartpay\Exceptions\ApiException(
                "Unable to decode Rabo Smart Pay response: '{$body}'."
            );
        }

        return $object;
    }

    /**
     * Retrieve the plain exception message.
     *
     * @return string
     */
    public function getPlainMessage()
    {
        return $this->plainMessage;
    }

    /**
     * Get the API error code.
     *
     * @return string|int|null
     */
    public function getApiErrorCode()
    {
        return $this->apiErrorCode;
    }

    /**
     * Get the API error message.
     *
     * @return string|null
     */
    public function getApiErrorMessage()
    {
        return $this->apiErrorMessage;
    }

    /**
     * Get a nicely formatted error message for display.
     *
     * @return string
     */
    public function getFormattedError()
    {
        $output = "SmartPay API Error\n";

        if ($this->apiErrorCode) {
            $output .= "Error Code: " . $this->apiErrorCode . "\n";
        }

        if ($this->apiErrorMessage) {
            $output .= "Message: " . $this->apiErrorMessage . "\n";
        }

        if ($this->raisedAt) {
            $output .= "Time: " . $this->raisedAt->format('Y-m-d H:i:s') . "\n";
        }

        return $output;
    }
}
