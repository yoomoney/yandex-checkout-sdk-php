<?php

namespace Tests\YandexCheckout\Model\PaymentData;

use YandexCheckout\Helpers\Random;
use YandexCheckout\Model\PaymentData\PaymentDataAndroidPay;
use YandexCheckout\Model\PaymentData\PaymentDataApplePay;

require_once __DIR__ . '/AbstractPaymentDataTest.php';

abstract class AbstractPaymentDataMobileTest extends AbstractPaymentDataTest
{
    /**
     * @dataProvider validPaymentDataDataProvider
     * @param $value
     */
    public function testGetSetPaymentData($value)
    {
        /** @var PaymentDataApplePay|PaymentDataAndroidPay $instance */
        $instance = $this->getTestInstance();

        self::assertNull($instance->getPaymentData());
        self::assertNull($instance->paymentData);

        $instance->setPaymentData($value);
        if ($value === null || $value === '') {
            self::assertNull($instance->getPaymentData());
            self::assertNull($instance->paymentData);
        } else {
            self::assertEquals($value, $instance->getPaymentData());
            self::assertEquals($value, $instance->paymentData);
        }

        $instance = $this->getTestInstance();
        $instance->paymentData = $value;
        if ($value === null || $value === '') {
            self::assertNull($instance->getPaymentData());
            self::assertNull($instance->paymentData);
        } else {
            self::assertEquals($value, $instance->getPaymentData());
            self::assertEquals($value, $instance->paymentData);
        }
    }

    /**
     * @dataProvider invalidPaymentDataDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidPaymentData($value)
    {
        /** @var PaymentDataApplePay|PaymentDataAndroidPay $instance */
        $instance = $this->getTestInstance();
        $instance->setPaymentData($value);
    }

    /**
     * @dataProvider invalidPaymentDataDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidPaymentData($value)
    {
        /** @var PaymentDataApplePay|PaymentDataAndroidPay $instance */
        $instance = $this->getTestInstance();
        $instance->paymentData = $value;
    }

    public function validPaymentDataDataProvider()
    {
        return array(
            array('http://test.ru'),
            array(Random::str(256)),
            array(Random::str(1024)),
        );
    }

    public function invalidPaymentDataDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass()),
        );
    }
}
