<?php

namespace Mageplaza\Osc\Controller\Index;

class Index extends \Magento\Checkout\Controller\Onepage
{
    protected $_checkoutHelper;

    /**
     *
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_checkoutHelper = $this->_objectManager->get('Mageplaza\Osc\Helper\Data');
        if (!$this->_checkoutHelper->isEnabled()) {
            $this->messageManager->addError(__('One step checkout is turned off.'));

            return $this->resultRedirectFactory->create()->setPath('checkout');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $this->_customerSession->regenerateId();
        $this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();

        $this->_initDefaultMethod();

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($this->_checkoutHelper->getConfig()->getCheckoutTitle());

        return $resultPage;
    }

    protected function _initDefaultMethod()
    {
        $quote           = $this->getOnepage()->getQuote();
        $shippingAddress = $quote->getShippingAddress();

        $defaultCountry = $this->_objectManager->get('Magento\Directory\Helper\Data')->getDefaultCountry();
        if(!$shippingAddress->getCountryId() && $defaultCountry){
            $quote->getBillingAddress()->setData('country_id', $defaultCountry)->save();
            $shippingAddress->setData('country_id', $defaultCountry)
                ->setCollectShippingRates(true)
                ->save();
        }

        if (!$shippingAddress->getShippingMethod()) {
            $this->_initDefaultShippingMethod($shippingAddress);
        }

        if (!$quote->getPayment()->getMethod()) {
            $this->_initDefaultPaymentMethod();
        }

        $quote->collectTotals()->save();
    }

    protected function _initDefaultShippingMethod($shippingAddress)
    {
        $shippingRates = $shippingAddress->collectShippingRates()->getAllShippingRates();
        $sizeOfRates   = sizeof($shippingRates);
        if ($sizeOfRates == 1) {
            $shippingMethod = $shippingRates[0]->getCode();
        } elseif ($sizeOfRates > 1) {
            $shippingMethod = $this->_checkoutHelper->getConfig()->getDefaultShippingMethod();
            if (!$shippingMethod || !$shippingAddress->getShippingRateByCode($shippingMethod)) {
                $shippingMethod = $shippingRates[0]->getCode();
            }
        }
        if (isset($shippingMethod)) {
            try {
                $this->getOnepage()->saveShippingMethod($shippingMethod);
            } catch(\Magento\Framework\Exception\LocalizedException $e){}
        }

        return $this;
    }

    protected function _initDefaultPaymentMethod()
    {
        $paymentHelper  = $this->_objectManager->get('Magento\Payment\Helper\Data');
        $quote = $this->getOnepage()->getQuote();
        $paymentMethods = $paymentHelper->getStoreMethods($quote->getStore()->getId(), $quote);
        if (sizeof($paymentMethods) == 1) {
            $currentPaymentMethod = current($paymentMethods);
            $paymentMethod        = $currentPaymentMethod->getCode();
        } elseif ($defaultPaymentMethod = $this->_checkoutHelper->getConfig()->getDefaultPaymentMethod()) {
            $paymentMethod = $defaultPaymentMethod;
        }

        if (isset($paymentMethod)) {
            try {
                $this->getOnepage()->savePayment(['method' => $paymentMethod]);
            } catch(\Magento\Framework\Exception\LocalizedException $e){}
        }

        return $this;
    }
}
