<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Worldline Fetch Transaction Response
 */
class FetchTransactionResponse extends AbstractResponse
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

        return $this->data->statusOutput->statusCategory == 'PENDING_CONNECT_OR_3RD_PARTY';
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

        return in_array($this->data->statusOutput->statusCategory, ['COMPLETED', 'REFUNDED']);
    }

    /**
     * Numeric status code (also in back office / report files)
     *
     * @return null|string
     */
    public function getCode()
    {
        return $this->data->statusOutput->statusCode ?? $this->data->errors[0]->errorCode ?? null;
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
