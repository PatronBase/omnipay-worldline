<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Tests\TestCase;

class FetchTransactionResponseTest extends TestCase
{
    public function testRefundError()
    {
        $response = new FetchTransactionResponse($this->getMockRequest(), (object) [
            'errorId' => '12345678-abcd-ef90-1234-567890abcdef-00000092',
            'errors' => [
                (object) [
                    'errorCode' => 50001130,
                    'category' => 'PAYMENT_PLATFORM_ERROR',
                    'code' => 1002,
                    'httpStatusCode' => 404,
                    'id' => 'UNKNOWN_PAYMENT_ID',
                    'message' => 'PaymentId has a wrong syntax',
                    'retriable' => false,
                ],
            ],
        ]);

        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(50001130, $response->getCode());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('12345678-abcd-ef90-1234-567890abcdef-00000092', $response->getMessage());
    }
}
