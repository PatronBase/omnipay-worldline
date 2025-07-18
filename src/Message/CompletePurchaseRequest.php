<?php

namespace Omnipay\Worldline\Message;

/**
 * Worldline Complete Purchase Request
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/HostedCheckout/operation/GetHostedCheckoutApi
 */
class CompletePurchaseRequest extends AbstractRequest
{
    public function getHostedCheckoutId()
    {
        return $this->getParameter('hostedCheckoutId');
    }

    public function setHostedCheckoutId($value)
    {
        return $this->setParameter('hostedCheckoutId', $value);
    }

    public function getData()
    {
        $this->validate('merchantId', 'hostedCheckoutId');

        return $this->httpRequest->request->all();
    }

    protected function createResponse($data)
    {
        return $this->response = new CompletePurchaseResponse($this, json_decode($data));
    }

    protected function getAction()
    {
        return '/v2/'.$this->getMerchantId().'/hostedcheckouts/'.$this->getHostedCheckoutId();
    }
}
