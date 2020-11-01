<?php

namespace Tests\YandexCheckout\Model\PaymentMethod;

use YandexCheckout\Model\PaymentMethod\PaymentMethodSbp;
use YandexCheckout\Model\PaymentMethodType;

class PaymentMethodSbpTest extends AbstractPaymentMethodTest
{
    /**
     * @return PaymentMethodSbp
     */
    protected function getTestInstance()
    {
        return new PaymentMethodSbp();
    }

    /**
     * @return string
     */
    protected function getExpectedType()
    {
        return PaymentMethodType::SBP;
    }
}