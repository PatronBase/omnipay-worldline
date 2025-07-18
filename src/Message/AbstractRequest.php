<?php

namespace Omnipay\Worldline\Message;

use DateTime;
use DateTimeZone;
use Money\Currency;
use Money\Number;
use Money\Parser\DecimalMoneyParser;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Base request for Worldline
 *
 * @see https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/authentication
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    /** @var string */
    protected $liveEndpoint = 'https://payment.direct.worldline-solutions.com';
    /** @var string */
    protected $testEndpoint = 'https://payment.preprod.direct.worldline-solutions.com';
    /** @var string HTTP verb used to make the request */
    protected $requestMethod = 'GET';

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

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
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

    /**
     * @param mixed $data
     * @return AbstractResponse
     */
    abstract protected function createResponse($data);

    /**
     * @return string
     */
    abstract protected function getAction();

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

    /**
     * Get integer version (sallest unit) of item price
     *
     * Copied from {@see BaseAbstractRequest::getAmountInteger()} & {@see BaseAbstractRequest::getMoney()}
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
