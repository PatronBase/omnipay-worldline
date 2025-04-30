<?php

namespace Omnipay\Worldline;

use Omnipay\Common\AbstractGateway;
use Omnipay\Worldline\Message\CompletePurchaseRequest;
use Omnipay\Worldline\Message\PurchaseRequest;

/**
 * Worldline Hosted Checkout Gateway
 *
 * @link http://www..worldline-solutions.com/
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/HostedCheckout
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Worldline';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiKey'     => '',
            'apiSecret'  => '',
            'merchantId' => '',
            'testMode'   => false,
        );
    }

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

    public function purchase(array $parameters = [])
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }
}
