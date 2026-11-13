<?php

namespace Codebrainbv\Smartpay\Endpoints;

use Codebrainbv\Smartpay\Resources\Order;
use Codebrainbv\Smartpay\Exceptions\ApiException;
use Codebrainbv\Smartpay\Endpoints\AbstractEndpoint;

class OrderEndpoint extends AbstractEndpoint
{
    // Payments
    protected $resourcePath = 'omnikassa-api/order/server/api/v2/order';

    /**
     * @return Order
     */
    protected function getResourceObject()
    {
        return new Order($this->client);
    }

    /**
     * Creates an order in the Rabo Smart Pay.
     *
     * @param array $data an array containing details on the payment
     *
     * @return Order
     *
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = [])
    {
        $data = $this->formatDataForApi($data);
        return $this->restCreate($data, $filters);
    }

    /**
     * Format the amount field for the API.
     * Converts decimal amount (e.g., 10.00) to cents with currency.
     *
     * @param array $data
     * @return array
     */
    private function formatDataForApi(array $data): array
    {
        if (!isset($data['amount'])) {
            return $data;
        }

        $amount = $data['amount'];

        // If amount is already an array, return as-is
        if (is_array($amount)) {
            return $data;
        }

        // Convert decimal to cents (10.00 -> 1000) and wrap in array with currency
        $amountInCents = (int)round((float)$amount * 100);
        $currency = $data['currency'] ?? 'EUR';

        $data['amount'] = [
            'amount' => $amountInCents,
            'currency' => $currency
        ];

        // Remove currency from root level if it was used for amount formatting
        if (isset($data['currency']) && ($data['currency'] === $currency)) {
            unset($data['currency']);
        }

        return $data;
    }

    /**
     * Retrieves an order from the Rabo Smart Pay.
     *
     * @param string $omnikassaOrderId the UUID of the order
     *
     * @return Order
     *
     * @throws ApiException
     */
    public function get(string $omnikassaOrderId)
    {
        if (empty($omnikassaOrderId)) {
            throw new ApiException("Missing order ID: '{$omnikassaOrderId}'.");
        }

        return $this->restRead($omnikassaOrderId);
    }
}
