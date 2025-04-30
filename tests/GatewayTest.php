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
    }

    public function testCompletePurchaseFailure()
    {
        $this->setMockHttpResponse('HostedCompletePurchaseFailure.txt');

        $options = array_merge($this->options, ['hostedCheckoutId' => '0000000001']);

        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('1234567890_0', $response->getTransactionReference());
        $this->assertSame('CANCELLED', $response->getMessage());
    }
}
