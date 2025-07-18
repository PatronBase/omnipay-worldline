<?php

namespace Omnipay\Worldline\Message;

/**
 * Worldline Refund Request
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/Payments/operation/RefundPaymentApi
 */
class RefundRequest extends AbstractRequest
{
    protected $requestMethod = 'POST';

    public function getData()
    {
        $this->validate('merchantId', 'amount', 'currency', 'transactionReference');

        $data = [
            'amountOfMoney' => [
                'amount' => $this->getAmountInteger(),
                'currencyCode' => $this->getCurrency(),
            ],
            'operationReferences' => [
                'merchantReference' => $this->getTransactionId(),
            ],
        ];

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new RefundResponse($this, json_decode($data));
    }

    protected function getAction()
    {
        return '/v2/'.$this->getMerchantId().'/payments/'.$this->getTransactionReference().'/refund';
    }
}
