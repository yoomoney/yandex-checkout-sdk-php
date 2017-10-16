<?php

namespace Tests\YandexCheckout\Model\PaymentData;

use YandexCheckout\Helpers\Random;
use YandexCheckout\Model\PaymentData\PaymentDataSberbank;
use YandexCheckout\Model\PaymentMethodType;

require_once __DIR__ . '/AbstractPaymentDataPhoneTest.php';

class PaymentDataSberbankTest extends AbstractPaymentDataPhoneTest
{
    /**
     * @return PaymentDataSberbank
     */
    protected function getTestInstance()
    {
        return new PaymentDataSberbank();
    }

    /**
     * @return string
     */
    protected function getExpectedType()
    {
        return PaymentMethodType::SBERBANK;
    }

    /**
     * @dataProvider validBindIdDataProvider
     * @param $value
     */
    public function testGetSetBindId($value)
    {
        $instance = $this->getTestInstance();

        self::assertNull($instance->getBindId());
        self::assertNull($instance->bindId);

        $instance->setBindId($value);
        self::assertEquals($value, $instance->getBindId());
        self::assertEquals($value, $instance->bindId);

        $instance = $this->getTestInstance();
        $instance->bindId = $value;
        self::assertEquals($value, $instance->getBindId());
        self::assertEquals($value, $instance->bindId);
    }

    /**
     * @dataProvider invalidBindIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidBindId($value)
    {
        $instance = $this->getTestInstance();
        $instance->setBindId($value);
    }

    /**
     * @dataProvider invalidBindIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidBindId($value)
    {
        $instance = $this->getTestInstance();
        $instance->bindId = $value;
    }

    public function validBindIdDataProvider()
    {
        return array(
            array('123'),
            array(Random::str(256)),
            array(Random::str(1024)),
        );
    }

    public function invalidBindIdDataProvider()
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