<?php

namespace Codebrainbv\Smartpay\Resources;

use Codebrainbv\Smartpay\Resources\BaseResource;
use Codebrainbv\Smartpay\Enums\PaymentStates;
use Codebrainbv\Smartpay\Enums\PaymentMethods;

class Status extends BaseResource
{
    /**
     * The unique identifier of the order at Rabo Smart Pay.
     *
     * @var string|null
     */
    public ?string $id;

    /**
     * The merchant order ID provided when creating the order.
     *
     * @var string
     */
    public string $merchantOrderId;

    /**
     * The status of the order.
     * Possible values: COMPLETED, PENDING, FAILED, CANCELLED
     *
     * @var string
     */
    public string $status;

    /**
     * The total amount of the order including currency.
     * Contains 'amount' (in cents) and 'currency' fields.
     *
     * @var array|null
     */
    public ?array $totalAmount;

    /**
     * Array of transactions associated with this order.
     * Each transaction contains payment details and status.
     *
     * @var array|null
     */
    public ?array $transactions;

    /**
     * Allowed properties for this resource.
     *
     * @var array
     */
    protected static $allowedProperties = [
        'id',
        'merchantOrderId',
        'status',
        'totalAmount',
        'transactions'
    ];

    /**
     * Is this order completed?
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === PaymentStates::STATUS_PAID;
    }

    /**
     * Is this order pending?
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === PaymentStates::STATUS_OPEN;
    }

    /**
     * Has this order failed?
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === PaymentStates::STATUS_FAILED;
    }

    /**
     * Is this order cancelled?
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === PaymentStates::STATUS_CANCELED;
    }

    /**
     * Get the order ID.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id ?? null;
    }

    /**
     * Get the merchant order ID.
     *
     * @return string|null
     */
    public function getMerchantOrderId()
    {
        return $this->merchantOrderId ?? null;
    }

    /**
     * Get the order status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status ?? null;
    }

    /**
     * Get the total amount as a formatted string (e.g., "€109.97").
     *
     * @return string|null
     */
    public function getFormattedTotalAmount()
    {
        if (empty($this->totalAmount['amount'])) {
            return null;
        }

        $amountInEuros = $this->totalAmount['amount'] / 100;

        return number_format($amountInEuros, 2, '.', ',');
    }

    /**
     * Get the total amount in cents.
     *
     * @return int|null
     */
    public function getTotalAmountInCents()
    {
        return $this->totalAmount['amount'] ?? null;
    }

    /**
     * Get the total amount currency.
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->totalAmount['currency'] ?? null;
    }

    /**
     * Get all transactions.
     *
     * @return array|null
     */
    public function getTransactions()
    {
        return $this->transactions ?? null;
    }

    /**
     * Get the first successful transaction, if any.
     *
     * @return array|null
     */
    public function getSuccessfulTransaction()
    {
        if (empty($this->transactions)) {
            return null;
        }

        foreach ($this->transactions as $transaction) {
            if (!empty($transaction['status']) && $transaction['status'] === PaymentStates::STATUS_PAID) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * Get the payment brand from the first successful transaction.
     *
     * @return string|null
     */
    public function getPaymentBrand()
    {
        $transaction = $this->getSuccessfulTransaction();
        return $transaction['paymentBrand'] ?? null;
    }

    /**
     * Get the payment method display name.
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        $brand = $this->getPaymentBrand();

        return match ($brand) {
            PaymentMethods::IDEAL->value => 'iDEAL',
            PaymentMethods::PAYPAL->value => 'PayPal',
            PaymentMethods::MASTERCARD->value => 'Mastercard',
            PaymentMethods::VISA->value => 'Visa',
            PaymentMethods::BANCONTACT->value => 'Bancontact',
            PaymentMethods::MAESTRO->value => 'Maestro',
            PaymentMethods::V_PAY->value => 'V PAY',
            PaymentMethods::CARDS->value => 'Card',
            default => $brand ?? 'Unknown',
        };
    }

    /**
     * Get the transaction creation timestamp.
     *
     * @return string|null
     */
    public function getTransactionCreatedAt()
    {
        $transaction = $this->getSuccessfulTransaction();
        return $transaction['createdAt'] ?? null;
    }

    /**
     * Get the transaction creation timestamp as a DateTime object.
     *
     * @return \DateTime|null
     */
    public function getTransactionCreatedAtDateTime()
    {
        $createdAt = $this->getTransactionCreatedAt();
        if (!$createdAt) {
            return null;
        }

        try {
            return new \DateTime($createdAt);
        } catch (\Exception $e) {
            return null;
        }
    }
}
