<?php

namespace Codebrainbv\Smartpay\States;

class PaymentStates
{
    /**
     * Payment of order is yet to be finalized.
     */
    public const STATUS_OPEN = 'IN_PROGRESS';

    /**
     * The payment was cancelled.
     */
    public const STATUS_CANCELED = 'CANCELLED';

    /**
     * Successful payment of the order.
     */
    public const STATUS_PAID = 'COMPLETED';

    /**
     * The consumer has not paid within the stipulated period.
     */
    public const STATUS_FAILED = 'EXPIRED';

    /**
     * Payment of order is been partially done.
     */
    public const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
}
