<?php

namespace Omnipay\Worldline\Message;

/**
 * Worldline Fetch Transaction Request
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/Payments/operation/GetPaymentDetailsApi
 */
class FetchTransactionRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('merchantId', 'transactionReference');
        return [];
    }

    protected function createResponse($data)
    {
        return $this->response = new FetchTransactionResponse($this, json_decode($data));
    }

    protected function getAction()
    {
        return '/v2/'.$this->getMerchantId().'/payments/'.$this->getTransactionReference().'/details';
    }
}
