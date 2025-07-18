<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Tests\TestCase;

class RefundResponseTest extends TestCase
{
    public function testRefundError()
    {
        $response = new RefundResponse($this->getMockRequest(), (object) [
            'errorId' => '12345678-abcd-ef90-1234-567890abcdef',
            'errors' => [
                (object) [
                    'errorCode' => 50001129,
                    'category' => 'PAYMENT_PLATFORM_ERROR',
                    'code' => 1020,
                    'httpStatusCode' => 400,
                    'id' => 'ACTION_NOT_ALLOWED_ON_TRANSACTION',
                    'message' => 'ACTION_NOT_ALLOWED_ON_TRANSACTION',
                    'retriable' => false,
                ],
            ],
        ]);

        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(50001129, $response->getCode());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('12345678-abcd-ef90-1234-567890abcdef', $response->getMessage());
    }
}
