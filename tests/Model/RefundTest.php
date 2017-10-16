<?php

namespace Tests\YandexCheckout\Model;

use PHPUnit\Framework\TestCase;
use YandexCheckout\Helpers\Random;
use YandexCheckout\Helpers\StringObject;
use YandexCheckout\Model\AmountInterface;
use YandexCheckout\Model\MonetaryAmount;
use YandexCheckout\Model\PaymentError;
use YandexCheckout\Model\ReceiptRegistrationStatus;
use YandexCheckout\Model\Refund;
use YandexCheckout\Model\RefundError;
use YandexCheckout\Model\RefundStatus;
use YandexCheckout\Model\Status;

class RefundTest extends TestCase
{
    /**
     * @dataProvider validIdDataProvider
     * @param string $value
     */
    public function testGetSetId($value)
    {
        $instance = new Refund();

        self::assertNull($instance->getId());
        self::assertNull($instance->id);

        $instance->setId($value);
        self::assertEquals((string)$value, $instance->getId());
        self::assertEquals((string)$value, $instance->id);

        $instance = new Refund();
        $instance->id = $value;
        self::assertEquals((string)$value, $instance->getId());
        self::assertEquals((string)$value, $instance->id);
    }

    public function validIdDataProvider()
    {
        $values = 'abcdefghijklmnopqrstuvwxyz';
        $values .= strtoupper($values) . '0123456789._-+';

        return array(
            array(Random::str(36, $values)),
            array(Random::str(36, $values)),
            array(new StringObject(Random::str(36, $values))),
            array(new StringObject(Random::str(36, $values))),
        );
    }

    /**
     * @dataProvider invalidIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidId($value)
    {
        $instance = new Refund();
        $instance->setId($value);
    }

    /**
     * @dataProvider invalidIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidId($value)
    {
        $instance = new Refund();
        $instance->id = $value;
    }

    /**
     * @return array
     */
    public function invalidIdDataProvider()
    {
        return array(
            array(''),
            array(null),
            array(array()),
            array(Random::str(1,35)),
            array(Random::str(1)),
            array(Random::str(35)),
            array(Random::str(37,48)),
            array(Random::str(37)),
            array(new \stdClass()),
            array(1),
            array(0),
            array(-1),
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider validIdDataProvider
     * @param string $value
     */
    public function testGetSetPaymentId($value)
    {
        $instance = new Refund();

        self::assertNull($instance->getPaymentId());
        self::assertNull($instance->paymentId);

        $instance->setPaymentId($value);
        self::assertEquals((string)$value, $instance->getPaymentId());
        self::assertEquals((string)$value, $instance->paymentId);

        $instance = new Refund();
        $instance->paymentId = $value;
        self::assertEquals((string)$value, $instance->getPaymentId());
        self::assertEquals((string)$value, $instance->paymentId);
    }

    /**
     * @dataProvider invalidIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidPaymentId($value)
    {
        $instance = new Refund();
        $instance->setPaymentId($value);
    }

    /**
     * @dataProvider invalidIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidPaymentId($value)
    {
        $instance = new Refund();
        $instance->paymentId = $value;
    }

    /**
     * @dataProvider validStatusDataProvider
     * @param string $value
     */
    public function testGetSetStatus($value)
    {
        $instance = new Refund();

        self::assertNull($instance->getStatus());
        self::assertNull($instance->status);

        $instance->setStatus($value);
        self::assertEquals((string)$value, $instance->getStatus());
        self::assertEquals((string)$value, $instance->status);

        $instance = new Refund();
        $instance->status = $value;
        self::assertEquals((string)$value, $instance->getStatus());
        self::assertEquals((string)$value, $instance->status);
    }

    /**
     * @return array
     */
    public function validStatusDataProvider()
    {
        $result = array();
        foreach (RefundStatus::getValidValues() as $value) {
            $result[] = array($value);
            $result[] = array(new StringObject($value));
        }
        return $result;
    }

    /**
     * @dataProvider invalidIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidStatus($value)
    {
        $instance = new Refund();
        $instance->setStatus($value);
    }

    /**
     * @dataProvider invalidIdDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidStatus($value)
    {
        $instance = new Refund();
        $instance->status = $value;
    }

    /**
     * @dataProvider validErrorDataProvider
     * @param RefundStatus $value
     */
    public function testGetSetError($value)
    {
        $instance = new Refund();

        self::assertNull($instance->getError());
        self::assertNull($instance->error);

        $instance->setError($value);
        self::assertSame($value, $instance->getError());
        self::assertSame($value, $instance->error);

        $instance = new Refund();
        $instance->error = $value;
        self::assertSame($value, $instance->getError());
        self::assertSame($value, $instance->error);
    }

    /**
     * @return array
     */
    public function validErrorDataProvider()
    {
        return array(
            array(new RefundError()),
        );
    }

    /**
     * @dataProvider invalidErrorDataProvider
     * @param $value
     */
    public function testSetInvalidError($value)
    {
        if (class_exists('TypeError')) {
            self::setExpectedException('TypeError');
            $instance = new Refund();
            $instance->setError($value);
        }
    }

    /**
     * @dataProvider invalidErrorDataProvider
     * @param $value
     */
    public function testSetterInvalidError($value)
    {
        if (class_exists('TypeError')) {
            self::setExpectedException('TypeError');
            $instance = new Refund();
            $instance->error = $value;
        }
    }

    /**
     * @return array
     */
    public function invalidErrorDataProvider()
    {
        return array(
            array(new Status()),
            array(new PaymentError()),
            array(null),
        );
    }

    /**
     * @dataProvider validCreatedAtDataProvider
     * @param mixed $value
     */
    public function testGetSetCreatedAt($value)
    {
        $instance = new Refund();

        if (is_numeric($value)) {
            $expected = $value;
        } elseif ($value instanceof \DateTime) {
            $expected = $value->getTimestamp();
        } else {
            $expected = strtotime((string)$value);
        }

        self::assertNull($instance->getCreatedAt());
        self::assertNull($instance->createdAt);

        $instance->setCreatedAt($value);
        self::assertSame($expected, $instance->getCreatedAt()->getTimestamp());
        self::assertSame($expected, $instance->createdAt->getTimestamp());

        $instance = new Refund();
        $instance->createdAt = $value;
        self::assertSame($expected, $instance->getCreatedAt()->getTimestamp());
        self::assertSame($expected, $instance->createdAt->getTimestamp());
    }

    /**
     * @return array
     */
    public function validCreatedAtDataProvider()
    {
        return array(
            array(new \DateTime()),
            array(new \DateTime(date(DATE_ATOM, Random::int(1, time())))),
            array(time()),
            array(Random::int(1, time())),
            array(date(DATE_ATOM)),
            array(date(DATE_ATOM, Random::int(1, time()))),
            array(new StringObject(date(DATE_ATOM))),
            array(new StringObject(date(DATE_ATOM, Random::int(1, time())))),
        );
    }

    /**
     * @dataProvider invalidCreatedAtDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidCreatedAt($value)
    {
        $instance = new Refund();
        $instance->setCreatedAt($value);
    }

    /**
     * @dataProvider invalidCreatedAtDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidCreatedAt($value)
    {
        $instance = new Refund();
        $instance->createdAt = $value;
    }

    /**
     * @return array
     */
    public function invalidCreatedAtDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(array()),
            array(new \stdClass()),
            array('test'),
        );
    }

    /**
     * @dataProvider validAuthorizedAtDataProvider
     * @param mixed $value
     */
    public function testGetSetAuthorizedAt($value)
    {
        $instance = new Refund();

        if (is_numeric($value)) {
            $expected = $value;
        } elseif ($value instanceof \DateTime) {
            $expected = $value->getTimestamp();
        } else {
            $expected = strtotime((string)$value);
        }

        self::assertNull($instance->getAuthorizedAt());
        self::assertNull($instance->authorizedAt);

        $instance->setAuthorizedAt($value);
        if ($value === null || $value === '') {
            self::assertNull($instance->getAuthorizedAt());
            self::assertNull($instance->authorizedAt);
        } else {
            self::assertSame($expected, $instance->getAuthorizedAt()->getTimestamp());
            self::assertSame($expected, $instance->authorizedAt->getTimestamp());
        }

        $instance = new Refund();
        $instance->authorizedAt = $value;
        if ($value === null || $value === '') {
            self::assertNull($instance->getAuthorizedAt());
            self::assertNull($instance->authorizedAt);
        } else {
            self::assertSame($expected, $instance->getAuthorizedAt()->getTimestamp());
            self::assertSame($expected, $instance->authorizedAt->getTimestamp());
        }
    }

    /**
     * @dataProvider invalidAuthorizedAtDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidAuthorizedAt($value)
    {
        $instance = new Refund();
        $instance->setAuthorizedAt($value);
    }

    /**
     * @dataProvider invalidAuthorizedAtDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidAuthorizedAt($value)
    {
        $instance = new Refund();
        $instance->authorizedAt = $value;
    }

    /**
     * @return array
     */
    public function invalidAuthorizedAtDataProvider()
    {
        return array(
            array(array()),
            array(new \stdClass()),
            array('test'),
        );
    }

    /**
     * @return array
     */
    public function validAuthorizedAtDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(new \DateTime()),
            array(new \DateTime(date(DATE_ATOM, Random::int(1, time())))),
            array(time()),
            array(Random::int(1, time())),
            array(date(DATE_ATOM)),
            array(date(DATE_ATOM, Random::int(1, time()))),
            array(new StringObject(date(DATE_ATOM))),
            array(new StringObject(date(DATE_ATOM, Random::int(1, time())))),
        );
    }

    /**
     * @dataProvider validAmountDataProvider
     * @param AmountInterface $value
     */
    public function testGetSetAmount($value)
    {
        $instance = new Refund();

        self::assertNull($instance->getAmount());
        self::assertNull($instance->amount);

        $instance->setAmount($value);
        self::assertSame($value, $instance->getAmount());
        self::assertSame($value, $instance->amount);

        $instance = new Refund();
        $instance->amount = $value;
        self::assertSame($value, $instance->getAmount());
        self::assertSame($value, $instance->amount);
    }

    /**
     * @return array
     */
    public function validAmountDataProvider()
    {
        return array(
            array(new MonetaryAmount(1)),
            array(new MonetaryAmount(Random::float(0.01, 9999999.99))),
        );
    }

    /**
     * @dataProvider invalidAmountDataProvider
     * @param mixed $value
     */
    public function testSetInvalidAmount($value)
    {
        if ($value instanceof AmountInterface) {
            self::setExpectedException('InvalidArgumentException');
            $instance = new Refund();
            $instance->setAmount($value);
        } elseif (class_exists('TypeError')) {
            self::setExpectedException('TypeError');
            $instance = new Refund();
            $instance->setAmount($value);
        }
    }

    /**
     * @dataProvider invalidAmountDataProvider
     * @param mixed $value
     */
    public function testSetterInvalidAmount($value)
    {
        if ($value instanceof AmountInterface) {
            self::setExpectedException('InvalidArgumentException');
            $instance = new Refund();
            $instance->amount = $value;
        } elseif (class_exists('TypeError')) {
            self::setExpectedException('TypeError');
            $instance = new Refund();
            $instance->amount = $value;
        }
    }

    /**
     * @return array
     */
    public function invalidAmountDataProvider()
    {
        return array(
            array(''),
            array(null),
            array(true),
            array(false),
            array(new MonetaryAmount()),
            array(array()),
            array(1),
            array(new \stdClass()),
        );
    }

    /**
     * @dataProvider validReceiptRegisteredDataProvider
     * @param string $value
     */
    public function testGetSetReceiptRegistered($value)
    {
        $instance = new Refund();

        self::assertNull($instance->getReceiptRegistration());
        self::assertNull($instance->receiptRegistration);

        $instance->setReceiptRegistration($value);
        self::assertEquals((string)$value, $instance->getReceiptRegistration());
        self::assertEquals((string)$value, $instance->receiptRegistration);

        $instance = new Refund();
        $instance->receiptRegistration = $value;
        self::assertEquals((string)$value, $instance->getReceiptRegistration());
        self::assertEquals((string)$value, $instance->receiptRegistration);
    }

    /**
     * @return array
     */
    public function validReceiptRegisteredDataProvider()
    {
        $result = array();
        foreach (ReceiptRegistrationStatus::getValidValues() as $value) {
            $result[] = array($value);
            $result[] = array(new StringObject($value));
        }
        return $result;
    }

    /**
     * @dataProvider invalidReceiptRegisteredDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetInvalidReceiptRegistered($value)
    {
        $instance = new Refund();
        $instance->setReceiptRegistration($value);
    }

    /**
     * @dataProvider invalidReceiptRegisteredDataProvider
     * @expectedException \InvalidArgumentException
     * @param $value
     */
    public function testSetterInvalidReceiptRegistered($value)
    {
        $instance = new Refund();
        $instance->receiptRegistration = $value;
    }

    /**
     * @return array
     */
    public function invalidReceiptRegisteredDataProvider()
    {
        return array(
            array(''),
            array(null),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass()),
            array(Random::str(1,10)),
            array(new StringObject(Random::str(1,10))),
        );
    }

    /**
     * @dataProvider validCommentDataProvider
     * @param string $value
     */
    public function testGetSetComment($value)
    {
        $instance = new Refund();

        self::assertNull($instance->getComment());
        self::assertNull($instance->comment);

        $instance->setComment($value);
        self::assertEquals((string)$value, $instance->getComment());
        self::assertEquals((string)$value, $instance->comment);

        $instance = new Refund();
        $instance->comment = $value;
        self::assertEquals((string)$value, $instance->getComment());
        self::assertEquals((string)$value, $instance->comment);
    }

    public function validCommentDataProvider()
    {
        return array(
            array(Random::str(1, 249)),
            array(new StringObject(Random::str(1, 249))),
            array(Random::str(250)),
            array(new StringObject(Random::str(250))),
        );
    }

    /**
     * @dataProvider invalidCommentDataProvider
     * @expectedException \InvalidArgumentException
     * @param mixed $value
     */
    public function testSetInvalidComment($value)
    {
        $instance = new Refund();
        $instance->setComment($value);
    }

    /**
     * @dataProvider invalidCommentDataProvider
     * @expectedException \InvalidArgumentException
     * @param mixed $value
     */
    public function testSetterInvalidComment($value)
    {
        $instance = new Refund();
        $instance->comment = $value;
    }

    public function invalidCommentDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(Random::str(251)),
            array(new StringObject(Random::str(251))),
            array(array()),
            array(true),
            array(false),
            array(new \stdClass()),
        );
    }
}