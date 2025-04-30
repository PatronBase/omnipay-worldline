<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Worldline Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data->createdPaymentOutput->paymentStatusCategory == 'SUCCESSFUL';
    }

    /**
     * Get the authorisation code if available.
     *
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->data->createdPaymentOutput->payment->id ?? null;
    }

    /**
     * Get the merchant response message if available.
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->data->createdPaymentOutput->payment->status;
    }
}
