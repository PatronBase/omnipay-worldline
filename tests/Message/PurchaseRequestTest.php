<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /** @var PurchaseRequest */
    private $request;
    
    /** @var mixed[] */
    protected $requiredParams = [
        'amount'           => '1.45',
        'apiKey'           => 'ABCDEF1234567890ABCD',
        'apiSecret'        => 'ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890',
        'currency'         => 'EUR',
        'merchantId'       => 'TestMerchant1',
    ];

    /** @var mixed[] */
    protected $moreParams = [];

    /** @var mixed[] */
    protected $allParams = [];

    public function setUp(): void
    {
        $this->moreParams = $this->requiredParams + [
            // unsupported?
            // 'cancelUrl'        => 'https://www.example.com/cancel',
            'clientIp'         => '192.0.0.0',
            'description'      => 'My sales items',
            // 'locale'           => 'en_UK',
            'merchantName'     => 'Test Merchant',
            'notifyUrl'        => 'https://www.example.com/notify',
            'returnUrl'        => 'https://www.example.com/return',
            'sessionTimeout'   => 100,
            'showResultPage'   => false,
            'transactionId'    => '123abc',
        ];

        $this->allParams = $this->moreParams + [
            'availablePaymentProducts' => [1, 2, 3],
            'card'  => new CreditCard([
                'email'            => "test@example.net",
                'shippingAddress1' => "Ship 1 Street",
                'shippingAddress2' => "Ship 2 Suburb",
                'shippingCity'     => "Ship City",
                'shippingPostcode' => "Ship Postcode",
                'shippingState'    => "Ship State",
                'shippingCountry'  => "Ship Country",
                'billingAddress1'  => "Bill 1 Street",
                'billingAddress2'  => "Bill 2 Suburb",
                'billingCity'      => "Bill City",
                'billingPostcode'  => "Bill Postcode",
                'billingState'     => "Bill State",
                'billingCountry'   => "Bill Country",
            ]),
            'excludedPaymentProducts' => [117, 125, 840],
            'items' => [
                [
                    'name' => 'Test Product',
                    'description' => 'A testing product',
                    'quantity' => '1',
                    'price' => '1.00',
                ],
                [
                    'name' => 'Test Fee',
                    'description' => 'A testing fee',
                    'quantity' => '1',
                    'price' => '0.45',
                ],
            ]
        ];

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->allParams);
    }

    public function testGetEndpoint()
    {
        $this->request->setTestMode(true);
        $this->assertSame(
            'https://payment.preprod.direct.worldline-solutions.com/v2/TestMerchant1/hostedcheckouts',
            $this->request->getEndpoint()
        );
        $this->request->setTestMode(false);
        $this->assertSame(
            'https://payment.direct.worldline-solutions.com/v2/TestMerchant1/hostedcheckouts',
            $this->request->getEndpoint()
        );
    }

    public function testGetApiKey()
    {
        $this->assertSame('ABCDEF1234567890ABCD', $this->request->getApiKey());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertArrayHasKey("hostedCheckoutSpecificInput", $data);
        $this->assertArrayHasKey("returnUrl", $data["hostedCheckoutSpecificInput"]);
        $this->assertSame("https://www.example.com/return", $data['hostedCheckoutSpecificInput']['returnUrl']);
        $this->assertArrayHasKey("paymentProductFilters", $data["hostedCheckoutSpecificInput"]);
        $this->assertArrayHasKey("exclude", $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]);
        $this->assertArrayHasKey("products", $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]["exclude"]);
        $this->assertEquals([117, 125, 840], $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]["exclude"]["products"]);
        $this->assertArrayHasKey("restrictTo", $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]);
        $this->assertArrayHasKey("products", $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]["restrictTo"]);
        $this->assertEquals([1, 2, 3], $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]["restrictTo"]["products"]);
        
        $this->assertArrayHasKey("order", $data);
        $this->assertArrayHasKey("amountOfMoney", $data["order"]);
        $this->assertArrayHasKey("references", $data["order"]);
        $this->assertArrayHasKey("shoppingCart", $data["order"]);

        $this->assertArrayHasKey("feedbacks", $data);
        $this->assertArrayHasKey("webhookUrl", $data["feedbacks"]);
        $this->assertSame("https://www.example.com/notify", $data['feedbacks']['webhookUrl']);
    }

    public function testGetDataWithoutAvailablePaymentProducts()
    {
        $params = $this->allParams;
        unset($params['availablePaymentProducts']);
        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($params);

        $data = $request->getData();

        $this->assertArrayHasKey("hostedCheckoutSpecificInput", $data);
        $this->assertArrayHasKey("paymentProductFilters", $data["hostedCheckoutSpecificInput"]);
        $this->assertArrayHasKey("exclude", $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]);
        $this->assertArrayHasKey("products", $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]["exclude"]);
        $this->assertEquals([117, 125, 840], $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]["exclude"]["products"]);
        $this->assertArrayNotHasKey("restrictTo", $data["hostedCheckoutSpecificInput"]["paymentProductFilters"]);
    }

    public function testGetItemPriceIntegerWithBadItemPrice()
    {
        $params = $this->allParams;
        $params['items'][0]['price'] = '1.2345';
        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($params);

        $this->expectException(InvalidRequestException::class);
        $data = $request->getData();
    }
}
