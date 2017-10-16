<?php

namespace Tests\YandexCheckout\Request\Payments\Payment;

use PHPUnit\Framework\TestCase;
use YandexCheckout\Model\CurrencyCode;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequest;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequestSerializer;

class CreateCaptureRequestSerializerTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     * @param array $options
     */
    public function testSerialize($options)
    {
        $serializer = new CreateCaptureRequestSerializer();
        $data = $serializer->serialize(CreateCaptureRequest::builder()->build($options));
        
        $expected = array(
            'amount' => array(
                'value'    => $options['amount'],
                'currency' => $options['currency'],
            )
        );
        self::assertEquals($expected, $data);
    }

    public function validDataProvider()
    {
        $result = array();
        $currencies = CurrencyCode::getValidValues();
        for ($i = 0; $i < 10; $i++) {
            $request = array(
                'amount'   => mt_rand(1, 1000000),
                'currency' => $currencies[mt_rand(0, count($currencies) - 1)],
            );
            $result[] = array($request);
        }
        return $result;
    }
}