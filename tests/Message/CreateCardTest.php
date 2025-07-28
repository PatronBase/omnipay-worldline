<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Tests\TestCase;

class CreateCardTest extends TestCase
{
    /** @var CreateCardRequest */
    private $request;
    
    /** @var mixed[] */
    protected $allParams = [
        'amount'           => '1.45',
        'apiKey'           => 'ABCDEF1234567890ABCD',
        'apiSecret'        => 'ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890',
        'currency'         => 'EUR',
        'merchantId'       => 'TestMerchant1',
    ];

    public function setUp(): void
    {
        $this->request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->allParams);
    }

    public function testNoAuthorizationMode()
    {
        $this->assertNull($this->request->getAuthorizationMode());

        $data = $this->request->getData();

        $this->assertArrayHasKey("cardPaymentMethodSpecificInput", $data);
        $this->assertArrayHasKey("authorizationMode", $data["cardPaymentMethodSpecificInput"]);
        $this->assertSame("PRE_AUTHORIZATION", $data["cardPaymentMethodSpecificInput"]['authorizationMode']);
    }

    public function testChangedAuthorizationMode()
    {
        $params = $this->allParams;
        $params['authorizationMode'] = 'FINAL_AUTHORIZATION';
        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($params);

        $data = $request->getData();

        $this->assertArrayHasKey("cardPaymentMethodSpecificInput", $data);
        $this->assertArrayHasKey("authorizationMode", $data["cardPaymentMethodSpecificInput"]);
        $this->assertSame("FINAL_AUTHORIZATION", $data["cardPaymentMethodSpecificInput"]['authorizationMode']);
    }

    public function testInvalidAuthorizationMode()
    {
        $params = $this->allParams;
        $params['authorizationMode'] = 'INVALID';
        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($params);

        $this->assertNull($this->request->getAuthorizationMode());

        $data = $request->getData();

        $this->assertArrayHasKey("cardPaymentMethodSpecificInput", $data);
        $this->assertArrayHasKey("authorizationMode", $data["cardPaymentMethodSpecificInput"]);
        $this->assertSame("PRE_AUTHORIZATION", $data["cardPaymentMethodSpecificInput"]['authorizationMode']);
    }
}
