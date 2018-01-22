<?php

/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Mageplaza
 * @package    Mageplaza_Osc
 * @version    3.0.0
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Block;

use Magento\Framework\View\Element\Template;

class Container extends Template
{
    protected $_objectManager;
    protected $_moduleManager;
    protected $_helperData;
    protected $_helperConfig;
    protected $_helperBlock;
    protected $_jsonHelper;
    protected $_customerSession;
    protected $_checkoutSession;
    protected $_storeManager;
    protected $_customer;
    protected $_quote;
    protected $_layoutProcessors;
    protected $_formKey;
    protected $_configProvider;

    public function __construct(
        Context $context,
        array $data = [],
        array $layoutProcessors = []
    ) {
        parent::__construct($context, $data);

        $this->_objectManager    = $context->getObjectManager();
        $this->_moduleManager    = $context->getModuleManager();
        $this->_helperData       = $context->getHelperData();
        $this->_helperConfig     = $context->getHelperConfig();
        $this->_helperBlock      = $context->getHelperBlock();
        $this->_jsonHelper       = $context->getJsonHelper();
        $this->_customerSession  = $context->getCustomerSession();
        $this->_checkoutSession  = $context->getCheckoutSession();
        $this->_storeManager     = $context->getStoreManager();
        $this->_formKey          = $context->getFormKey();
        $this->_layoutProcessors = $layoutProcessors;
        $this->_configProvider   = $context->getConfigProvider();
    }

    /**
     * @return array
     */
    public function getCheckoutConfig()
    {
        return $this->_configProvider->getConfig();
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->_formKey;
    }


    public function getHelperConfig()
    {
        return $this->_helperConfig;
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->_layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }

        return \Zend_Json::encode($this->jsLayout);
    }

    public function getHelperData()
    {
        return $this->_helperData;
    }

    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * get Checkout block mapping
     *
     * @return string
     */
    public function getBlockMapping()
    {
        $blocks       = [];
        $blockMapping = $this->_helperBlock->getReloadSection();
        foreach ($blockMapping as $action => $sections) {
            $blocks[$action] = $sections;
        }

        return $this->_jsonHelper->jsonEncode($blocks);
    }

    /**
     * @param bool|true $increment
     * @return bool|int|string
     */
    public function getNumbering($increment = true)
    {
        return $this->_helperData->getNumbering($increment);
    }

    /**
     * @return string
     */
    public function getActionPattern()
    {
        $actionPattern = '/osc\/ajax\/([^\/]+)\//';

        return $actionPattern;
    }

    /**
     * @return string
     */
    public function getSaveFormUrl()
    {
        return $this->getUrl('onestepcheckout/ajax/saveForm', ['_secure' => $this->isSecure()]);
    }

    /**
     * @return string
     */
    public function getCheckoutTitle()
    {
        return $this->escapeHtml($this->_helperConfig->getCheckoutTitle());
    }

    /**
     * @return string
     */
    public function getCheckoutDescription()
    {
        return $this->escapeHtml($this->_helperConfig->getCheckoutDescription());
    }

    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = $this->_customerSession->getCustomer();
        }

        return $this->_customer;
    }

    /**
     * @return mixed
     */
    public function getOnePage()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage');
    }

    public function isVirtualQuote()
    {
        return $this->getQuote()->isVirtual();
    }

    public function getQuote()
    {
        return $this->getOnePage()->getQuote();
//        return $this->_checkoutSession->getQuote();
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    public function getFormData()
    {
        return $this->_checkoutSession->getData('osc_form_values');
    }

    /**
     * @return mixed
     */
    protected function isSecure()
    {
        return $this->_storeManager->getStore()->isCurrentlySecure();
    }

    public function getSavePaymentUrl()
    {
        return $this->getUrl('onestepcheckout/ajax/savePayment', ['_secure' => $this->isSecure()]);
    }

}