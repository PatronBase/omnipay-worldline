<?php

namespace Omnipay\Worldline\Message;

/**
 * Worldline (Hosted) Create Card Request
 *
 * Creates a card token via the hosted checkout by creating an Authorize Request and forcing the 'Tokenize' property.
 * This can still be used to make purchases if the authorizationMode is supplied as 'SALE' or it's captured separately.
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/HostedCheckout/operation/CreateHostedCheckoutApi
 */
class CreateCardRequest extends PurchaseRequest
{
    protected $authorizationMode = 'PRE_AUTHORIZATION';

    public function getAuthorizationMode()
    {
        return $this->getParameter('authorizationMode');
    }

    public function setAuthorizationMode($value)
    {
        if (!in_array($value, ['FINAL_AUTHORIZATION', 'PRE_AUTHORIZATION', 'SALE'])) {
            $value = null;
        }
        return $this->setParameter('authorizationMode', $value);
    }

    public function getData()
    {
        $this->setAmount($this->getAmount() ?: '1.00');
        $this->setTokenize(true);

        if ($this->getAuthorizationMode()) {
            $this->authorizationMode = $this->getAuthorizationMode();
        }

        return parent::getData();
    }
}
