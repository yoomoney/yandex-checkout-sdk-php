<?php

namespace Tests\YandexCheckout\Model\PaymentData;

use YandexCheckout\Model\PaymentData\PaymentDataSbp;
use YandexCheckout\Model\PaymentMethodType;

class PaymentDataSbpTest extends AbstractPaymentDataTest
{
    /**
     * @return PaymentDataSbp
     */
    protected function getTestInstance()
    {
        return new PaymentDataSbp();
    }

    /**
     * @return string
     */
    protected function getExpectedType()
    {
        return PaymentMethodType::SBP;
    }
}