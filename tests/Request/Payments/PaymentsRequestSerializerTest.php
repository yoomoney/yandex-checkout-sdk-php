<?php

namespace Tests\YandexCheckout\Request\Payments;

use PHPUnit\Framework\TestCase;
use YandexCheckout\Model\PaymentStatus;
use YandexCheckout\Request\Payments\PaymentsRequest;
use YandexCheckout\Request\Payments\PaymentsRequestSerializer;

class PaymentsRequestSerializerTest extends TestCase
{
    private $fieldMap = array(
        'createdAtGte'       => 'created_at.gte',
        'createdAtGt'        => 'created_at.gt',
        'createdAtLte'       => 'created_at.lte',
        'createdAtLt'        => 'created_at.lt',
        'status'             => 'status',
        'limit'              => 'limit',
        'cursor'             => 'cursor',
    );

    /**
     * @dataProvider validDataProvider
     * @param $options
     */
    public function testSerialize($options)
    {
        $serializer = new PaymentsRequestSerializer();
        $data       = $serializer->serialize(PaymentsRequest::builder()->build($options));

        $expected = array();
        foreach ($this->fieldMap as $field => $mapped) {
            if (isset($options[$field])) {
                $value = $options[$field];
                if (!empty($value)) {
                    $expected[$mapped] = $value instanceof \DateTime ? $value->format(DATE_ATOM) : $value;
                }
            }
        }
        self::assertEquals($expected, $data);
    }

    public function validDataProvider()
    {
        $result   = array(
            array(
                array(),
            ),
            array(
                array(
                    'createdAtGte'       => '',
                    'createdAtGt'        => '',
                    'createdAtLte'       => '',
                    'createdAtLt'        => '',
                    'limit'              => 0,
                    'status'             => '',
                    'cursor'             => '',
                ),
            ),
        );
        $statuses = PaymentStatus::getValidValues();
        for ($i = 0; $i < 10; $i++) {
            $request  = array(
                'createdAtGte'       => date(DATE_ATOM, mt_rand(1, time())),
                'createdAtGt'        => date(DATE_ATOM, mt_rand(1, time())),
                'createdAtLte'       => date(DATE_ATOM, mt_rand(1, time())),
                'createdAtLt'        => date(DATE_ATOM, mt_rand(1, time())),
                'limit'              => mt_rand(1, 100),
                'status'             => $statuses[mt_rand(0, count($statuses) - 1)],
                'cursor'             => $this->randomString(mt_rand(1, 30)),
            );
            $result[] = array($request);
        }
        return $result;
    }

    private function randomString($length, $any = true)
    {
        static $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-+_.';

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            if ($any) {
                $char = chr(mt_rand(32, 126));
            } else {
                $rnd  = mt_rand(0, strlen($chars) - 1);
                $char = substr($chars, $rnd, 1);
            }
            $result .= $char;
        }
        return $result;
    }
}