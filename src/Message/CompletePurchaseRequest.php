<?php

namespace Omnipay\Worldline\Message;

/**
 * Worldline Complete Purchase Request
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/HostedCheckout/operation/GetHostedCheckoutApi
 */
class CompletePurchaseRequest extends PurchaseRequest
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

    protected function getRequestMethod()
    {
        return 'GET';
    }
}
