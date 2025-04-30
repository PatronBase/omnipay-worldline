<?php

namespace Omnipay\Worldline\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Worldline Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return empty($this->data->errors);
    }

    public function getMessage()
    {
        if (!$this->isRedirect()) {
            // get the details from the first error in the array, if it's available
            $error = isset($this->data->errors) ? reset($this->data->errors) : null;
            return $error->id ?? $this->data->errorId ?? null;
        }
    }

    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return $this->data->redirectUrl;
        }
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }

    public function getHostedCheckoutId()
    {
        return $this->getData()->hostedCheckoutId ?? null;
    }
}
