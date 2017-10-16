<?php

namespace Tests\YandexCheckout\Request\Payments\Payment;

use PHPUnit\Framework\TestCase;
use YandexCheckout\Helpers\Random;
use YandexCheckout\Model\AmountInterface;
use YandexCheckout\Model\CurrencyCode;
use YandexCheckout\Model\MonetaryAmount;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequestBuilder;

class CreateCaptureRequestBuilderTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     * @param $options
     */
    public function testSetAmountValue($options)
    {
        $builder = new CreateCaptureRequestBuilder();
        try {
            $builder->build();
        } catch (\Exception $e) {
            $builder->setAmount($options['amount']);
            $instance = $builder->build();

            self::assertNotNull($instance->getAmount());
            self::assertEquals($options['amount'], $instance->getAmount()->getValue());

            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider validAmountDataProvider
     * @param AmountInterface $amount
     */
    public function testSetAmount($amount)
    {
        $builder = new CreateCaptureRequestBuilder();
        try {
            $builder->build();
        } catch (\Exception $e) {
            $builder->setAmount($amount);
            $instance = $builder->build();

            self::assertNotNull($instance->getAmount());
            self::assertEquals($amount->getValue(), $instance->getAmount()->getValue());
            self::assertEquals($amount->getCurrency(), $instance->getAmount()->getCurrency());

            $builder->setAmount(array(
                'value' => $amount->getValue(),
                'currency' => $amount->getCurrency(),
            ));
            $instance = $builder->build();

            self::assertNotNull($instance->getAmount());
            self::assertEquals($amount->getValue(), $instance->getAmount()->getValue());
            self::assertEquals($amount->getCurrency(), $instance->getAmount()->getCurrency());

            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider invalidAmountDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidAmount($value)
    {
        $builder = new CreateCaptureRequestBuilder();
        $builder->setAmount($value);
    }

    /**
     * @dataProvider validDataProvider
     * @param $options
     */
    public function testSetAmountCurrency($options)
    {
        $builder = new CreateCaptureRequestBuilder();

        try {
            $builder->build();
        } catch (\Exception $e) {
            $builder->setCurrency($options['currency']);
            $instance = $builder->build(array('amount' => mt_rand(1, 100)));

            self::assertNotNull($instance->getAmount());
            self::assertEquals($options['currency'], $instance->getAmount()->getCurrency());

            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider invalidCurrencyDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidCurrency($value)
    {
        $builder = new CreateCaptureRequestBuilder();
        $builder->setCurrency($value);
    }

    /**
     * @dataProvider validDataProvider
     * @param $options
     */
    public function testBuild($options)
    {
        $builder = new CreateCaptureRequestBuilder();
        $instance = $builder->build($options);
        self::assertNotNull($instance->getAmount());
        self::assertEquals($options['amount'], $instance->getAmount()->getValue());
        self::assertEquals($options['currency'], $instance->getAmount()->getCurrency());
    }

    public function validDataProvider()
    {
        $result = array();
        for ($i = 0; $i < 10; $i++) {
            $request = array(
                'amount'   => Random::int(1, 1000000),
                'currency' => Random::value(CurrencyCode::getValidValues()),
            );
            $result[] = array($request);
        }
        return $result;
    }

    public function validAmountDataProvider()
    {
        return array(
            array(new MonetaryAmount(Random::int(1, 1000000))),
            array(new MonetaryAmount(Random::int(1, 1000000)), Random::value(CurrencyCode::getValidValues())),
        );
    }

    public function invalidAmountDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(-1),
            array(Random::str(10)),
            array(new \stdClass()),
            array(true),
            array(false),
        );
    }

    public function invalidCurrencyDataProvider()
    {
        return array(
            array(array()),
            array(null),
            array(''),
            array(-1),
            array(new \stdClass()),
            array(Random::str(10)),
            array(true),
            array(false),
        );
    }
}