<?php

namespace Omnipay\Worldline\Message;

/**
 * Worldline Purchase Request
 *
 * @see https://docs.direct.worldline-solutions.com/en/api-reference#tag/HostedCheckout/operation/CreateHostedCheckoutApi
 */
class PurchaseRequest extends AbstractRequest
{
    /** @var string  Can be "FINAL_AUTHORIZATION" "PRE_AUTHORIZATION" or "SALE" */
    protected $authorizationMode = 'SALE';
    protected $requestMethod = 'POST';

    public function getAvailablePaymentProducts()
    {
        return $this->getParameter('availablePaymentProducts');
    }

    /**
     * @param int[] $value  @see https://docs.direct.worldline-solutions.com/en/payment-methods-and-features/
     */
    public function setAvailablePaymentProducts($value)
    {
        return $this->setParameter('availablePaymentProducts', $value);
    }

    public function getExcludedPaymentProducts()
    {
        return $this->getParameter('excludedPaymentProducts');
    }

    /**
     * @param int[] $value  @see https://docs.direct.worldline-solutions.com/en/payment-methods-and-features/
     */
    public function setExcludedPaymentProducts($value)
    {
        return $this->setParameter('excludedPaymentProducts', $value);
    }

    public function getMerchantName()
    {
        return $this->getParameter('merchantName');
    }

    public function setMerchantName($value)
    {
        return $this->setParameter('merchantName', $value);
    }

    public function getShowResultPage()
    {
        return $this->getParameter('showResultPage');
    }

    public function setShowResultPage($value)
    {
        return $this->setParameter('showResultPage', $value);
    }

    public function getSessionTimeout()
    {
        return $this->getParameter('sessionTimeout');
    }

    /**
     * Timeout is in minutes, default 180
     */
    public function setSessionTimeout($value)
    {
        return $this->setParameter('sessionTimeout', $value);
    }

    public function getTransactionChannel()
    {
        return $this->getParameter('transactionChannel');
    }

    /**
     * Transaction channel can only be either 'ECOMMERCE' or 'MOTO'
     */
    public function setTransactionChannel($value)
    {
        if (!in_array($value, ['ECOMMERCE', 'MOTO'])) {
            $value = null;
        }
        return $this->setParameter('transactionChannel', $value);
    }

    public function getData()
    {
        $this->validate('merchantId', 'amount', 'currency');

        $formattedItems = [];
        $items = $this->getItems();
        if ($items) {
            foreach ($items as $item) {
                $itemPrice = $this->getItemPriceInteger($item);
                $formattedItems[] = [
                    'amountOfMoney' => [
                        'amount' => $item->getQuantity() * $itemPrice,
                        'currencyCode' => $this->getCurrency(),
                    ],
                    'orderLineDetails' => [
                        'productName' => $item->getName(),
                        'productPrice' => $itemPrice,
                        'quantity' => (int) $item->getQuantity(),
                    ],
                ];
            }
        }

        $data = [
            'cardPaymentMethodSpecificInput' => [
                'authorizationMode' => $this->authorizationMode ?? 'SALE',
                'transactionChannel' => $this->getTransactionChannel() ?? 'ECOMMERCE',
            ],
            'hostedCheckoutSpecificInput' => [
                // if adding locale, validate locale against known formats
                // @see https://docs.direct.worldline-solutions.com/en/integration/basic-integration-methods/hosted-checkout-page#chooselanguageversion
                // 'locale' => 'en_UK',
                'returnUrl' => $this->getReturnUrl(),
            ],
            'order' => [
                'amountOfMoney' => [
                    'amount' => $this->getAmountInteger(),
                    'currencyCode' => $this->getCurrency(),
                ],
                'references' => [
                    'descriptor' => $this->getMerchantName(),
                    'merchantReference' => $this->getTransactionId(),
                ],
                'shoppingCart' => [
                    'items' => $formattedItems,
                ],
            ],
        ];

        if ($this->getAvailablePaymentProducts() !== null) {
            if (!isset($data['hostedCheckoutSpecificInput']['paymentProductFilters'])) {
                $data['hostedCheckoutSpecificInput']['paymentProductFilters'] = [];
            }
            $data['hostedCheckoutSpecificInput']['paymentProductFilters']['restrictTo'] = [
                'products' => $this->getAvailablePaymentProducts(),
            ];
        }

        if ($this->getExcludedPaymentProducts() !== null) {
            if (!isset($data['hostedCheckoutSpecificInput']['paymentProductFilters'])) {
                $data['hostedCheckoutSpecificInput']['paymentProductFilters'] = [];
            }
            $data['hostedCheckoutSpecificInput']['paymentProductFilters']['exclude'] = [
                'products' => $this->getExcludedPaymentProducts(),
            ];
        }

        if ($this->getShowResultPage() !== null) {
            $data['hostedCheckoutSpecificInput']['showResultPage'] = (bool) $this->getShowResultPage();
        }

        if ($this->getSessionTimeout() !== null) {
            $data['hostedCheckoutSpecificInput']['sessionTimeout'] = (int) $this->getSessionTimeout();
        }

        if ($this->getNotifyUrl() !== null) {
            $data['feedbacks'] = [
                'webhookUrl' => $this->getNotifyUrl(),
            ];
        }

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, json_decode($data));
    }

    protected function getAction()
    {
        return '/v2/'.$this->getMerchantId().'/hostedcheckouts';
    }
}
