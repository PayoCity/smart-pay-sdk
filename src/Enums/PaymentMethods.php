<?php

namespace Codebrainbv\Smartpay\Enums;

enum PaymentMethods: string
{
    case IDEAL = 'IDEAL';
    case PAYPAL = 'PAYPAL';
    case MASTERCARD = 'MASTERCARD';
    case VISA = 'VISA';
    case BANCONTACT = 'BANCONTACT';
    case MAESTRO = 'MAESTRO';
    case V_PAY = 'V_PAY';
    case CARDS = 'CARDS';
}
