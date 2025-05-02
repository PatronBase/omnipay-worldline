<?php

namespace Omnipay\Worldline\Message;

use DateTime;
use DateTimeZone;
use Money\Currency;
use Money\Number;
use Money\Parser\DecimalMoneyParser;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Worldline Purchase Request
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/HostedCheckout/operation/CreateHostedCheckoutApi
 */
class PurchaseRequest extends AbstractRequest
{
    /** @var string */
    protected $liveEndpoint = 'https://payment.direct.worldline-solutions.com';
    /** @var string */
    protected $testEndpoint = 'https://payment.preprod.direct.worldline-solutions.com';

    /** @var string  Can be "FINAL_AUTHORIZATION" "PRE_AUTHORIZATION" or "SALE" */
    protected $authorizationMode = 'SALE';
    protected $requestMethod = 'POST';

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getApiSecret()
    {
        return $this->getParameter('apiSecret');
    }

    public function setApiSecret($value)
    {
        return $this->setParameter('apiSecret', $value);
    }

    public function getAvailablePaymentProducts()
    {
        return $this->getParameter('availablePaymentProducts');
    }

    /**
     * @param int[] $value  @see https://docs.direct.worldline-solutions.com/en/payment-methods-and-features/
     */
    public function setAvailablePaymentProducts($value)
    {
        return $this->setParameter('availablePaymentProducts', $value);
    }

    public function getExcludedPaymentProducts()
    {
        return $this->getParameter('excludedPaymentProducts');
    }

    /**
     * @param int[] $value  @see https://docs.direct.worldline-solutions.com/en/payment-methods-and-features/
     */
    public function setExcludedPaymentProducts($value)
    {
        return $this->setParameter('excludedPaymentProducts', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantName()
    {
        return $this->getParameter('merchantName');
    }

    public function setMerchantName($value)
    {
        return $this->setParameter('merchantName', $value);
    }

    public function getShowResultPage()
    {
        return $this->getParameter('showResultPage');
    }

    public function setShowResultPage($value)
    {
        return $this->setParameter('showResultPage', $value);
    }

    public function getSessionTimeout()
    {
        return $this->getParameter('sessionTimeout');
    }

    /**
     * Timeout is in minutes, default 180
     */
    public function setSessionTimeout($value)
    {
        return $this->setParameter('sessionTimeout', $value);
    }

    public function getData()
    {
        $this->validate('merchantId', 'amount', 'currency');

        $formattedItems = [];
        $items = $this->getItems();
        if ($items) {
            foreach ($items as $item) {
                $itemPrice = $this->getItemPriceInteger($item);
                $formattedItems[] = [
                    'amountOfMoney' => [
                        'amount' => $item->getQuantity() * $itemPrice,
                        'currencyCode' => $this->getCurrency(),
                    ],
                    'orderLineDetails' => [
                        'productName' => $item->getName(),
                        'productPrice' => $itemPrice,
                        'quantity' => (int) $item->getQuantity(),
                    ],
                ];
            }
        }

        $data = [
            'cardPaymentMethodSpecificInput' => [
                'authorizationMode' => 'SALE',
                'transactionChannel' => 'ECOMMERCE',
            ],
            'hostedCheckoutSpecificInput' => [
                // if adding locale, validate locale against known formats
                // @see https://docs.direct.worldline-solutions.com/en/integration/basic-integration-methods/hosted-checkout-page#chooselanguageversion
                // 'locale' => 'en_UK',
                'returnUrl' => $this->getReturnUrl(),
            ],
            'order' => [
                'amountOfMoney' => [
                    'amount' => $this->getAmountInteger(),
                    'currencyCode' => $this->getCurrency(),
                ],
                'references' => [
                    'descriptor' => $this->getMerchantName(),
                    'merchantReference' => $this->getTransactionId(),
                ],
                'shoppingCart' => [
                    'items' => $formattedItems,
                ],
            ],
        ];

        if ($this->getAvailablePaymentProducts() !== null) {
            if (!isset($data['hostedCheckoutSpecificInput']['paymentProductFilters'])) {
                $data['hostedCheckoutSpecificInput']['paymentProductFilters'] = [];
            }
            $data['hostedCheckoutSpecificInput']['paymentProductFilters']['restrictTo'] = [
                'products' => $this->getAvailablePaymentProducts(),
            ];
        }

        if ($this->getExcludedPaymentProducts() !== null) {
            if (!isset($data['hostedCheckoutSpecificInput']['paymentProductFilters'])) {
                $data['hostedCheckoutSpecificInput']['paymentProductFilters'] = [];
            }
            $data['hostedCheckoutSpecificInput']['paymentProductFilters']['exclude'] = [
                'products' => $this->getExcludedPaymentProducts(),
            ];
        }

        if ($this->getShowResultPage() !== null) {
            $data['hostedCheckoutSpecificInput']['showResultPage'] = (bool) $this->getShowResultPage();
        }

        if ($this->getSessionTimeout() !== null) {
            $data['hostedCheckoutSpecificInput']['sessionTimeout'] = (int) $this->getSessionTimeout();
        }

        if ($this->getNotifyUrl() !== null) {
            $data['feedbacks'] = [
                'webhookUrl' => $this->getNotifyUrl(),
            ];
        }

        return $data;
    }

    public function getEndpoint()
    {
        return ($this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint).$this->getAction();
    }

    public function sendData($data)
    {
        $contentType = $this->requestMethod == 'POST' ? 'application/json; charset=utf-8' : '';
        $now = new DateTime('now', new DateTimeZone('GMT'));
        $dateTime = $now->format("D, d M Y H:i:s T");
        $endpointAction = $this->getAction();

        $message = $this->requestMethod."\n".$contentType."\n".$dateTime."\n".$endpointAction."\n";
        $signature = $this->createSignature($message, $this->getApiSecret());

        $headers = [
            'Content-Type' => $contentType,
            'Authorization' => 'GCS v1HMAC:'.$this->getApiKey().':'.$signature,
            'Date' => $dateTime,
        ];

        $body = json_encode($data);

        $httpResponse = $this->httpClient->request(
            $this->requestMethod,
            $this->getEndpoint(),
            $headers,
            $body
        );

        return $this->createResponse($httpResponse->getBody()->getContents());
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, json_decode($data));
    }

    /**
     * Create signature hash used to verify messages
     *
     * @param string $message  The message to encrypt
     * @param string $key      The base64-encoded key used to encrypt the message
     *
     * @return string Generated signature
     */
    protected function createSignature($message, $key)
    {
        return base64_encode(hash_hmac('sha256', $message, $key, true));
    }

    protected function getAction()
    {
        return '/v2/'.$this->getMerchantId().'/hostedcheckouts';
    }

    /**
     * Get integer version (sallest unit) of item price
     *
     * Copied from {@see AbstractRequest::getAmountInteger()} & {@see AbstractRequest::getMoney()}
     */
    protected function getItemPriceInteger($item)
    {
        $currencyCode = $this->getCurrency() ?: 'USD';
        $currency = new Currency($currencyCode);
        $amount = $item->getPrice();

        $moneyParser = new DecimalMoneyParser($this->getCurrencies());
        $number = Number::fromString($amount);
        // Check for rounding that may occur if too many significant decimal digits are supplied.
        $decimal_count = strlen($number->getFractionalPart());
        $subunit = $this->getCurrencies()->subunitFor($currency);
        if ($decimal_count > $subunit) {
            throw new InvalidRequestException('Amount precision is too high for currency.');
        }
        $money = $moneyParser->parse((string) $number, $currency);

        return (int) $money->getAmount();
    }
}
