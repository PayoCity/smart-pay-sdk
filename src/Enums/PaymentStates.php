<?php

namespace Codebrainbv\Smartpay\Enums;

enum PaymentStates: string
{
    /**
     * Payment of order is yet to be finalized.
     */
    case STATUS_OPEN = 'IN_PROGRESS';

    /**
     * The payment was cancelled.
     */
    case STATUS_CANCELED = 'CANCELLED';

    /**
     * Successful payment of the order.
     */
    case STATUS_PAID = 'COMPLETED';

    /**
     * The consumer has not paid within the stipulated period.
     */
    case STATUS_FAILED = 'EXPIRED';

    /**
     * Payment of order is been partially done.
     */
    case STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
}
