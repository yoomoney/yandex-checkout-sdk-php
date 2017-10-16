<?php

namespace Tests\YandexCheckout\Model;

use PHPUnit\Framework\TestCase;
use YandexCheckout\Model\RefundError;

class RefundErrorTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     *
     * @param $value
     */
    public function testGetSetCode($value)
    {
        $instance = new RefundError();

        self::assertEquals(null, $instance->getCode());
        self::assertEquals(null, $instance->code);
        $instance->setCode($value);
        self::assertEquals($value, $instance->getCode());
        self::assertEquals($value, $instance->code);

        $instance = new RefundError();
        $instance->code = $value;
        self::assertEquals($value, $instance->getCode());
        self::assertEquals($value, $instance->code);
    }

    /**
     * @dataProvider invalidDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidCode($value)
    {
        $instance = new RefundError();
        $instance->setCode($value);
    }

    /**
     * @dataProvider invalidDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidCode($value)
    {
        $instance = new RefundError();
        $instance->code = $value;
    }

    /**
     * @dataProvider validDataProvider
     * @param $code
     * @param $value
     */
    public function testGetSetDescription($code, $value)
    {
        $instance = new RefundError();

        self::assertEquals(null, $instance->getDescription());
        self::assertEquals(null, $instance->description);
        $instance->setDescription($value);
        self::assertEquals($value, $instance->getDescription());
        self::assertEquals($value, $instance->description);

        $instance = new RefundError();
        $instance->description = $value;
        self::assertEquals($value, $instance->getDescription());
        self::assertEquals($value, $instance->description);
    }

    public function validDataProvider()
    {
        $result = array();
        for ($i = 0; $i < 10; $i++) {
            $result[] = array(uniqid(), uniqid());
        }
        return $result;
    }

    public function invalidDataProvider()
    {
        return array(
            array(null),
            array(''),
        );
    }
}