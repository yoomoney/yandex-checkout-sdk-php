<?php

namespace Tests\YandexCheckout\Model;

use PHPUnit\Framework\TestCase;
use YandexCheckout\Helpers\Random;
use YandexCheckout\Helpers\StringObject;
use YandexCheckout\Model\PaymentError;
use YandexCheckout\Model\PaymentErrorCode;

class PaymentErrorTest extends TestCase
{
    /**
     * @dataProvider validCodeDataProvider
     * @param $value
     */
    public function testGetSetCode($value)
    {
        $instance = new PaymentError();

        self::assertEquals(null, $instance->getCode());
        self::assertEquals(null, $instance->code);
        $instance->setCode($value);
        self::assertEquals($value, $instance->getCode());
        self::assertEquals($value, $instance->code);

        $instance = new PaymentError();
        $instance->code = $value;
        self::assertEquals($value, $instance->getCode());
        self::assertEquals($value, $instance->code);
    }

    /**
     * @return array
     */
    public function validCodeDataProvider()
    {
        $result = array();
        foreach (PaymentErrorCode::getValidValues() as $code) {
            $result[] = array($code);
        }
        foreach (PaymentErrorCode::getValidValues() as $code) {
            $result[] = array(new StringObject($code));
        }
        return $result;
    }

    /**
     * @dataProvider invalidCodeDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidCode($value)
    {
        $instance = new PaymentError();
        $instance->setCode($value);
    }

    /**
     * @dataProvider invalidCodeDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidCode($value)
    {
        $instance = new PaymentError();
        $instance->code = $value;
    }

    public function invalidCodeDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(Random::str(10)),
            array(array()),
            array(true),
            array(false),
            array(0),
            array(1),
            array(-1),
            array(Random::float()),
            array(new \stdClass()),
        );
    }

    /**
     * @dataProvider validDescriptionDataProvider
     * @param string|null $value
     */
    public function testGetSetDescription($value)
    {
        $instance = new PaymentError();

        self::assertNull($instance->getDescription());
        self::assertNull($instance->description);
        $instance->setDescription($value);
        if ($value === null || $value === '') {
            self::assertNull($instance->getDescription());
            self::assertNull($instance->description);
        } else {
            self::assertEquals($value, $instance->getDescription());
            self::assertEquals($value, $instance->description);
        }

        $instance = new PaymentError();
        $instance->description = $value;
        if ($value === null || $value === '') {
            self::assertNull($instance->getDescription());
            self::assertNull($instance->description);
        } else {
            self::assertEquals($value, $instance->getDescription());
            self::assertEquals($value, $instance->description);
        }
    }

    public function validDescriptionDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(Random::str(1, 249)),
            array(Random::str(249)),
            array(new StringObject(Random::str(1, 249))),
            array(new StringObject(Random::str(249))),
        );
    }

    /**
     * @dataProvider invalidDescriptionDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidDescription($value)
    {
        $instance = new PaymentError();
        $instance->setDescription($value);
    }

    /**
     * @dataProvider invalidDescriptionDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidDescription($value)
    {
        $instance = new PaymentError();
        $instance->description = $value;
    }

    public function invalidDescriptionDataProvider()
    {
        return array(
            array(array()),
            array(true),
            array(false),
            array(new \stdClass()),
        );
    }
}