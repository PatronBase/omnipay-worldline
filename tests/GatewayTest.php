<?php

namespace Omnipay\Worldline;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var array */
    protected $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'amount'        => '1.45',
            'currency'      => 'EUR',
            'merchantId'    => 'TestMerchant1',
            'merchantName'  => 'Test Merchant',
            'notifyUrl'     => 'https://www.example.com/notify',
            'returnUrl'     => 'https://www.example.com/return',
            'apiKey'        => 'ABCDEF1234567890ABCD',
            'apiSecret'     => 'ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890',
            'transactionId' => '123abc',
            'testMode'      => true,
        );
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('HostedPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getMessage());
        $this->assertSame('https://payment.preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/abcdef1234567890abcdef1234567890', $response->getRedirectUrl());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertNull($response->getRedirectData());
        $this->assertSame('0000000001', $response->getHostedCheckoutId());
        $this->assertNull($response->getTransactionReference());
    }

    public function testCompletePurchaseSuccess()
    {
        $this->setMockHttpResponse('HostedCompletePurchaseSuccess.txt');

        $options = array_merge($this->options, ['hostedCheckoutId' => '0000000001']);

        $response = $this->gateway->completePurchase($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('1234567890_0', $response->getTransactionReference());
        $this->assertSame('PENDING_CAPTURE', $response->getMessage());
        $this->assertSame('12345678-90ab-cdef-1234-567890abcdef', $response->getCardReference());
    }

    public function testCompletePurchaseFailure()
    {
        $this->setMockHttpResponse('HostedCompletePurchaseFailure.txt');

        $options = array_merge($this->options, ['hostedCheckoutId' => '0000000001']);

        $response = $this->gateway->completePurchase($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('1234567890_0', $response->getTransactionReference());
        $this->assertSame('CANCELLED', $response->getMessage());
        $this->assertNull($response->getCardReference());
    }

    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $options = $this->options + ['transactionReference' => '0000000001_0'];

        $response = $this->gateway->refund($options)->send();

        $this->assertFalse($response->isPending());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('REFUNDED', $response->getMessage());
        $this->assertSame(8, $response->getCode());
        $this->assertSame('0000000001_1', $response->getTransactionReference());
    }

    public function testRefundPending()
    {
        $this->setMockHttpResponse('RefundPending.txt');

        $options = $this->options + ['transactionReference' => '0000000001_0'];

        $response = $this->gateway->refund($options)->send();

        $this->assertTrue($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('REFUND_REQUESTED', $response->getMessage());
        $this->assertSame(81, $response->getCode());
        $this->assertSame('0000000001_1', $response->getTransactionReference());
    }

    public function testRefundFailure()
    {
        $this->setMockHttpResponse('RefundFailure.txt');

        $options = $this->options + ['transactionReference' => '0000000001_0'];

        $response = $this->gateway->refund($options)->send();

        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('11111111-2222-3333-4444-abcdefabcdef', $response->getMessage());
        $this->assertSame('50001130', $response->getCode());
        $this->assertNull($response->getTransactionReference());
    }

    public function testFetchTransactionSuccess()
    {
        $this->setMockHttpResponse('FetchTransactionSuccess.txt');

        $options = $this->options + ['transactionReference' => '0000000001_0'];

        $response = $this->gateway->fetchTransaction($options)->send();

        $this->assertFalse($response->isPending());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('CAPTURED', $response->getMessage());
        $this->assertSame(9, $response->getCode());
        $this->assertSame('0000000001_0', $response->getTransactionReference());
    }
}
