<?php


use PHPUnit\Framework\TestCase;
use YandexCheckout\Client\YandexMoneyApi;
use YandexCheckout\Request\PaymentOptionsRequest;
use YandexCheckout\Request\PaymentOptionsResponse;
use YandexCheckout\Request\PaymentOptionsResponseItem;
use YandexCheckout\Request\Payments\CreatePaymentResponse;
use YandexCheckout\Request\Payments\CreatePaymentRequest;
use YandexCheckout\Request\Payments\Payment\CancelResponse;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequest;
use YandexCheckout\Request\Payments\Payment\CreateCaptureResponse;
use YandexCheckout\Request\Payments\PaymentResponse;
use YandexCheckout\Request\Payments\PaymentsRequest;
use YandexCheckout\Request\Payments\PaymentsResponse;
use YandexCheckout\Request\Refunds\CreateRefundRequest;
use YandexCheckout\Request\Refunds\CreateRefundResponse;
use YandexCheckout\Request\Refunds\RefundResponse;
use YandexCheckout\Request\Refunds\RefundsRequest;
use YandexCheckout\Request\Refunds\RefundsResponse;

class YandexMoneyApiTest extends TestCase
{
    /**
     * @dataProvider paymentOptionsDataProvider
     * @param $paymentOptionsRequest
     */
    public function testPaymentOptions($paymentOptionsRequest)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('paymentOptionsFixtures.json'),
                array('http_code' => 200)
            ));

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getPaymentOptions($paymentOptionsRequest);

        self::assertSame($curlClientStub, $apiClient->getApiClient());
        $this->assertTrue($response instanceof PaymentOptionsResponse);
        foreach ($response->getItems() as $item) {
            $this->assertTrue($item instanceof PaymentOptionsResponseItem);
        }

        $items = $response->getItems();
        $item = $items[0];

        $this->assertTrue($item->getExtraFee());
        $this->assertEquals("yandex_money", $item->getPaymentMethodType());
        $this->assertEquals(array("redirect"), $item->getConfirmationTypes());
        $this->assertEquals("10.00", $item->getCharge()->getValue());
        $this->assertEquals("RUB", $item->getCharge()->getCurrency());
        $this->assertEquals("10.00", $item->getFee()->getValue());
        $this->assertEquals("RUB", $item->getFee()->getCurrency());
    }

    public function paymentOptionsDataProvider()
    {
        return array(
            array(null),
            array(PaymentOptionsRequest::builder()->setAccountId('123')->build()),
            array(
                array(
                    'account_id' => '123',
                )
            )
        );
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidPaymentOptions($httpCode, $errorResponse, $requiredException)
    {
        $paymentOptionsRequest = PaymentOptionsRequest::builder()->setAccountId('123')->build();
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->getPaymentOptions($paymentOptionsRequest);
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    public function testCreatePayment()
    {
        $payment = CreatePaymentRequest::builder()
            ->setAmount(123)
            ->setPaymentToken(\YandexCheckout\Helpers\Random::str(36))
            ->build();

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('createPaymentFixtures.json'),
                array('http_code' => 200)
            ));

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment($payment, 123);

        self::assertSame($curlClientStub, $apiClient->getApiClient());
        self::assertTrue($response instanceof CreatePaymentResponse);

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('createPaymentFixtures.json'),
                array('http_code' => 200)
            ));

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment(array(
                'amount' => array(
                    'value' => 123,
                    'currency' => 'USD',
                ),
                'payment_token' => \YandexCheckout\Helpers\Random::str(36),
            ), 123);

        self::assertSame($curlClientStub, $apiClient->getApiClient());
        self::assertTrue($response instanceof CreatePaymentResponse);

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"type":"error","code":"request_accepted","retry_after":123}',
                array('http_code' => 202)
            ));

        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment($payment, 123);
        self::assertNull($response);
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidCreatePayment($httpCode, $errorResponse, $requiredException)
    {
        $payment = CreatePaymentRequest::builder()
            ->setAmount(123)
            ->setPaymentToken(\YandexCheckout\Helpers\Random::str(36))
            ->build();
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->createPayment($payment);
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider paymentsListDataProvider
     * @param mixed $request
     */
    public function testPaymentsList($request)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('getPaymentsFixtures.json'),
                array('http_code' => 200)
            ));

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getPayments($request);

        $this->assertTrue($response instanceof PaymentsResponse);
    }

    public function paymentsListDataProvider()
    {
        return array(
            array(null),
            array(PaymentsRequest::builder()->setAccountId(12)->build()),
            array(array(
                'account_id' => 12,
            ))
        );
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidPaymentsList($httpCode, $errorResponse, $requiredException)
    {
        $payments = PaymentsRequest::builder()->setAccountId(12)->build();
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->getPayments($payments);
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider paymentInfoDataProvider
     */
    public function testGetPaymentInfo($paymentId)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($paymentId === null ? self::never() : self::once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('paymentInfoFixtures.json'),
                array('http_code' => 200)
            ));

        $apiClient = new YandexMoneyApi();

        if ($paymentId === null) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getPaymentInfo($paymentId);

        self::assertTrue($response instanceof PaymentResponse);
    }

    public function paymentInfoDataProvider()
    {
        return array(
            array(null),
            array(\YandexCheckout\Helpers\Random::str(36))
        );
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidGetPaymentInfo($httpCode, $errorResponse, $requiredException)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->getPaymentInfo(\YandexCheckout\Helpers\Random::str(36));
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    public function testCapturePayment()
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('capturePaymentFixtures.json'),
                array('http_code' => 200)
            ));

        $capturePaymentRequest = array(
            'amount' => array(
                'value' => 123,
                'currency' => 'EUR',
            )
        );

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->capturePayment($capturePaymentRequest, '1ddd77af-0bd7-500d-895b-c475c55fdefc', 123);

        $this->assertTrue($response instanceof CreateCaptureResponse);

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('capturePaymentFixtures.json'),
                array('http_code' => 200)
            ));

        $capturePaymentRequest = CreateCaptureRequest::builder()->setAmount(10)->build();

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->capturePayment($capturePaymentRequest, '1ddd77af-0bd7-500d-895b-c475c55fdefc', 123);

        $this->assertTrue($response instanceof CreateCaptureResponse);

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"type":"error","code":"request_accepted","retry_after":123}',
                array('http_code' => 202)
            ));

        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->capturePayment($capturePaymentRequest, '1ddd77af-0bd7-500d-895b-c475c55fdefc', 123);
        self::assertNull($response);

        try {
            $apiClient->capturePayment($capturePaymentRequest, null);
        } catch (\InvalidArgumentException $e) {
            // it's ok
            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidCapturePayment($httpCode, $errorResponse, $requiredException)
    {
        $capturePaymentRequest = CreateCaptureRequest::builder()->setAmount(10)->build();
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->capturePayment($capturePaymentRequest, '1ddd77af-0bd7-500d-895b-c475c55fdefc', 123);
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider cancelPaymentDataProvider
     * @param mixed $paymentId
     */
    public function testCancelPayment($paymentId)
    {
        $invalid = $paymentId === null || strlen($paymentId) != 36;
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($invalid ? self::never() : self::once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('cancelPaymentFixtures.json'),
                array('http_code' => 200)
            ));

        if ($invalid) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->cancelPayment($paymentId, 123);

        $this->assertTrue($response instanceof CancelResponse);
    }

    public function cancelPaymentDataProvider()
    {
        return array(
            array(null),
            array(123),
            array(\YandexCheckout\Helpers\Random::str(36)),
        );
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidCancelPayment($httpCode, $errorResponse, $requiredException)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->cancelPayment(\YandexCheckout\Helpers\Random::str(36), 123);
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider refundsDataProvider
     * @param mixed $refundsRequest
     */
    public function testGetRefunds($refundsRequest)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects(self::once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('refundsInfoFixtures.json'),
                array('http_code' => 200)
            ));

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getRefunds($refundsRequest);

        $this->assertTrue($response instanceof RefundsResponse);
    }

    public function refundsDataProvider()
    {
        return array(
            array(null),
            array(RefundsRequest::builder()->setAccountId(123)->build()),
            array(array(
                'account_id' => 123,
            )),
        );
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidGetRefunds($httpCode, $errorResponse, $requiredException)
    {
        $refundsRequest = RefundsRequest::builder()->setAccountId(123)->build();
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->getRefunds($refundsRequest);
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    public function testCreateRefund()
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('createRefundFixtures.json'),
                array('http_code' => 200)
            ));

        $refundRequest = CreateRefundRequest::builder()->setPaymentId('1ddd77af-0bd7-500d-895b-c475c55fdefc')->setAmount(123)->build();

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createRefund($refundRequest, 123);

        $this->assertTrue($response instanceof CreateRefundResponse);

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('createRefundFixtures.json'),
                array('http_code' => 200)
            ));

        $refundRequest = array(
            'payment_id' => '1ddd77af-0bd7-500d-895b-c475c55fdefc',
            'amount' => array(
                'value' => 321,
                'currency' => 'RUB',
            )
        );

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createRefund($refundRequest, 123);

        $this->assertTrue($response instanceof CreateRefundResponse);

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"type":"error","code":"request_accepted","retry_after":123}',
                array('http_code' => 202)
            ));

        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createRefund($refundRequest, 123);
        self::assertNull($response);
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidCreateRefund($httpCode, $errorResponse, $requiredException)
    {
        $refundRequest = CreateRefundRequest::builder()->setPaymentId('1ddd77af-0bd7-500d-895b-c475c55fdefc')->setAmount(123)->build();
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->createRefund($refundRequest, 123);
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    public function testRefundInfo()
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $this->getFixtures('refundInfoFixtures.json'),
                array('http_code' => 200)
            ));

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getRefundInfo(\YandexCheckout\Helpers\Random::str(36));

        $this->assertTrue($response instanceof RefundResponse);

        try {
            $apiClient->getRefundInfo(null);
        } catch (InvalidArgumentException $e) {
            // it's ok
            return;
        }
        self::fail('Exception not thrown');
    }

    /**
     * @dataProvider errorResponseDataProvider
     * @param $httpCode
     * @param $errorResponse
     * @param $requiredException
     */
    public function testInvalidRefundInfo($httpCode, $errorResponse, $requiredException)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                $errorResponse,
                array('http_code' => $httpCode)
            ));

        $apiClient = new YandexMoneyApi();
        $apiClient->setApiClient($curlClientStub)->setAuth('shopId', 'shopPassword');
        try {
            $apiClient->getRefundInfo(\YandexCheckout\Helpers\Random::str(36));
        } catch (\Exception $e) {
            self::assertInstanceOf($requiredException, $e);
            return;
        }
        self::fail('Exception not thrown');
    }

    public function testApiException()
    {
        $payment = CreatePaymentRequest::builder()
            ->setAmount(123)
            ->setPaymentToken(\YandexCheckout\Helpers\Random::str(36))
            ->build();

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                'unknown response here',
                array('http_code' => 444)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\ApiException');

        $apiClient = new YandexMoneyApi();
        $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment($payment, 123);
    }

    public function testBadRequestException()
    {
        $payment = CreatePaymentRequest::builder()
            ->setAmount(123)
            ->setPaymentToken(\YandexCheckout\Helpers\Random::str(36))
            ->build();

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"description": "error_msg", "code": "error_code", "parameter_name": "parameter_name"}',
                array('http_code' => 400)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\BadApiRequestException');

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment($payment, 123);
    }

    public function testTechnicalErrorException()
    {
        $payment = CreatePaymentRequest::builder()
            ->setAmount(123)
            ->setPaymentToken(\YandexCheckout\Helpers\Random::str(36))
            ->build();

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"description": "error_msg", "code": "error_code"}',
                array('http_code' => 500)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\InternalServerError');

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment($payment, 123);
    }

    public function testUnauthorizedException()
    {
        $payment = CreatePaymentRequest::builder()
            ->setAmount(123)
            ->setPaymentToken(\YandexCheckout\Helpers\Random::str(36))
            ->build();

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"description": "error_msg"}',
                array('http_code' => 401)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\UnauthorizedException');

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment($payment, 123);
    }

    public function testForbiddenException()
    {
        $payment = CreatePaymentRequest::builder()
            ->setAmount(123)
            ->setPaymentToken(\YandexCheckout\Helpers\Random::str(36))
            ->build();

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"description": "error_msg","error_code": "error_code", "parameter_name": "parameter_name", "operation_name": "operation_name"}',
                array('http_code' => 403)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\ForbiddenException');

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->createPayment($payment, 123);
    }

    public function testNotFoundException()
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"description": "error_msg","error_code": "error_code", "parameter_name": "parameter_name", "operation_name": "operation_name"}',
                array('http_code' => 404)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\NotFoundException');

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getPaymentInfo(\YandexCheckout\Helpers\Random::str(36));
    }

    public function testToManyRequestsException()
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"description": "error_msg","error_code": "error_code", "parameter_name": "parameter_name", "operation_name": "operation_name"}',
                array('http_code' => 429)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\TooManyRequestsException');

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getPaymentInfo(\YandexCheckout\Helpers\Random::str(36));
    }
    
    public function testConfig()
    {
        $apiClient = new YandexMoneyApi();
        $apiClient->setConfig(array(
            'url' => 'test'
        ));

        $this->assertEquals(array('url' => 'test'), $apiClient->getConfig());
    }

    public function testSetLogger()
    {
        $wrapped = new ArrayLogger();
        $logger = new \YandexCheckout\Common\LoggerWrapper($wrapped);

        $apiClient = new YandexMoneyApi();
        $apiClient->setLogger($logger);

        $clientMock = $this->getMockBuilder('YandexCheckout\Client\ApiClientInterface')
            ->setMethods(array('setLogger', 'setConfig', 'call'))
            ->disableOriginalConstructor()
            ->getMock();
        $expectedLoggers = array();
        $clientMock->expects(self::exactly(3))->method('setLogger')->willReturnCallback(function ($logger) use(&$expectedLoggers) {
            $expectedLoggers[] = $logger;
        });
        $clientMock->expects(self::once())->method('setConfig')->willReturn($clientMock);

        $apiClient->setApiClient($clientMock);
        self::assertSame($expectedLoggers[0], $logger);

        $apiClient->setLogger($wrapped);
        $apiClient->setLogger(function ($level, $log, $context = array()) use ($wrapped) {
            $wrapped->log($level, $log, $context);
        });
    }

    public function testEncodeData()
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturn(array(
                array('Header-Name' => 'HeaderValue'),
                '{"invalid":"json"',
                array('http_code' => 200)
            ));
        $this->setExpectedException('YandexCheckout\Common\Exceptions\JsonException');

        $apiClient = new YandexMoneyApi();
        $response = $apiClient
            ->setApiClient($curlClientStub)
            ->setAuth('shopId', 'shopPassword')
            ->getPaymentInfo(\YandexCheckout\Helpers\Random::str(36));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getCurlClientStub()
    {
        $clientStub = $this->getMockBuilder('YandexCheckout\Client\CurlClient')
            ->setMethods(array('sendRequest'))
            ->getMock();

        return $clientStub;
    }

    public function errorResponseDataProvider()
    {
        return array(
            array(\YandexCheckout\Common\Exceptions\BadApiRequestException::HTTP_CODE, '{}', 'YandexCheckout\Common\Exceptions\BadApiRequestException'),
            array(\YandexCheckout\Common\Exceptions\ForbiddenException::HTTP_CODE, '{}', 'YandexCheckout\Common\Exceptions\ForbiddenException'),
            array(\YandexCheckout\Common\Exceptions\UnauthorizedException::HTTP_CODE, '{}', 'YandexCheckout\Common\Exceptions\UnauthorizedException'),
            array(\YandexCheckout\Common\Exceptions\InternalServerError::HTTP_CODE, '{}', 'YandexCheckout\Common\Exceptions\InternalServerError'),
        );
    }

    /**
     * @return bool|string
     */
    private function getFixtures($fileName)
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $fileName);
    }
}

class ArrayLogger
{
    private $lastLog;

    public function log($level, $message, $context)
    {
        $this->lastLog = array($level, $message, $context);
    }

    public function getLastLog()
    {
        return $this->lastLog;
    }
}