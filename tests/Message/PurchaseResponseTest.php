<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    public function testPurchaseError()
    {
        $response = new PurchaseResponse($this->getMockRequest(), (object) [
            'errorId' => '12345678-abcd-ef90-1234-567890abcdef',
            'errors' => [
                (object) [
                    'errorCode' => 50001111,
                    'category' => 'DIRECT_PLATFORM_ERROR',
                    'code' => 1007,
                    'httpStatusCode' => 404,
                    'id' => 'UNKNOWN_PRODUCT_ID',
                    'message' => 'UNKNOWN_PRODUCT_ID',
                    'retriable' => false,
                ],
            ],
        ]);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getRedirectUrl());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertNull($response->getRedirectData());
        $this->assertNull($response->getHostedCheckoutId());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('UNKNOWN_PRODUCT_ID', $response->getMessage());
    }
}
