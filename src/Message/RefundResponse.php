<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Worldline Refund Response
 */
class RefundResponse extends AbstractResponse
{
    /**
     * Is the response pending success or failure?
     *
     * @return boolean
     */
    public function isPending()
    {
        if (empty($this->data) || isset($this->data->errorId)) {
            return false;
        }

        return $this->data->status == 'REFUND_REQUESTED';
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        if (empty($this->data) || isset($this->data->errorId)) {
            return false;
        }

        return $this->data->status == 'REFUNDED';
    }

    /**
     * Numeric status code (also in back office / report files)
     *
     * @return null|string
     */
    public function getCode()
    {
        return $this->data->statusOutput->statusCode
            ?? $this->data->refundResult->statusOutput->statusCode
            ?? $this->data->errors[0]->errorCode
            ?? null;
    }

    /**
     * Get the authorisation code if available.
     *
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->data->id ?? null;
    }

    /**
     * Get the merchant response message if available.
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->data->status ?? $this->data->errorId ?? null;
    }
}
