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
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Helper;

use Magento\Checkout\Helper\Data as HelperData;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\Wishlist;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Newsletter\Model\Subscriber;

class Data extends AbstractHelper
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    /**
     * @var Attribute
     */
    protected $_entityAttribute;

    /**
     * @var Config
     */
    protected $_helperConfig;

    /**
     * @var Collection
     */
    protected $_productCollection;

    /**
     * @var UrlInterface
     */
    protected $_priceCurrency;
    protected $_objectManager;
    protected $_subscriber;

    public function __construct(Context $context,
                                HelperData $helperData,
                                StoreManagerInterface $modelStoreManagerInterface,
                                Attribute $entityAttribute,
                                Config $helperConfig,
                                Collection $productCollection,
                                PriceCurrencyInterface $priceCurrency,
                                ObjectManagerInterface $objectManager,
                                Subscriber $subscriber
    ) {
        $this->_helperData                 = $helperData;
        $this->_modelStoreManagerInterface = $modelStoreManagerInterface;
        $this->_entityAttribute            = $entityAttribute;
        $this->_helperConfig               = $helperConfig;
        $this->_productCollection          = $productCollection;
        $this->_priceCurrency              = $priceCurrency;
        $this->_objectManager              = $objectManager;
        $this->_subscriber                 = $subscriber;
        parent::__construct($context);
    }

    public function getConfig()
    {
        return $this->_helperConfig;
    }

    public function isEnabled($store = null)
    {
        return $this->getConfig()->isEnabled($store);
    }

    const BLOCK_NUMBER_STORAGE_KEY = 'mc-osc-block-number';
    const SECURED_CHECKOUT_URL_REWRITE_ID_PATH = 'secure_checkout_rewrite_url';
    const SECURED_CHECKOUT_URL_REWRITE_TARGET_PATH = 'onestepcheckout_index_index';
    protected $currentNumber = '';

    public function isCustomerMustBeLogged()
    {
        $helper = $this->_helperData;
        if (method_exists($helper, 'isCustomerMustBeLogged')) {
            return $helper->isCustomerMustBeLogged();
        }

        return false;
    }

    /**
     * @param string $email
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getCustomerByEmail($email, $websiteId = null)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = ObjectManager::getInstance()->create(
            'Magento\Customer\Model\Customer'
        );
        if (!$websiteId) {
            $customer->setWebsiteId($this->_modelStoreManagerInterface->getWebsite()->getId());
        } else {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer && $customer->getId()) {
            return $customer;
        }

        return false;
    }

    public function getGrandTotal($quote)
    {
        $grandTotal = $quote->getGrandTotal();

        return $this->_modelStoreManagerInterface->getStore()->getCurrentCurrency()->format($grandTotal, [], false);
    }
    /*
     *Customer attribute
     *
    */
    /**
     * Return available customer attribute form as select options
     *
     * @throws \Magento\Framework\Exception
     */
    public function getAttributeFormOptions()
    {
        throw new \Exception(__('Use helper with defined EAV entity'));
    }

    /**
     * Default attribute entity type code
     *
     * @throws \Magento\Framework\Exception
     */
    protected function _getEntityTypeCode()
    {
        throw new \Exception(__('Use helper with defined EAV entity'));
    }

    public function getAttributeFrontendLabel($attribute_code, $entity_type = 1)
    {
        return $this->_entityAttribute->loadByCode($entity_type, $attribute_code)->getFrontendLabel();
    }

    /**
     * @param        $extensionName
     * @param        $extVersion
     * @param string $operator
     * @return bool|mixed
     */
    public function checkExtensionVersion($extensionName, $extVersion, $operator = '>=')
    {
        if ($this->isExtensionInstalled($extensionName)
            && ($version = Mage::getConfig()->getModuleConfig($extensionName)->version)
        ) {
            return version_compare($version, $extVersion, $operator);
        }

        return false;
    }

    /**
     * Removes empty values from the array given
     *
     * @param mixed $data Array to inspect or data to be placed in new array as first value
     * @return array Array processed
     */
    public static function noEmptyValues($data)
    {
        $result = [];
        if (is_array($data)) {
            foreach ($data as $a) {
                if ($a) {
                    $result[] = $a;
                }
            }
        } else {
            $result = $data ? [] : [$data];
        }

        return $result;
    }

    public function getNumbering($increment = true)
    {
        $configHelper = $this->_helperConfig;
        if (!$configHelper->isShowNumbering()) {
            return false;
        }
        if (!$this->currentNumber) {
            $this->currentNumber = 0;
        }
        if ($increment) {
            $this->currentNumber++;
        }

        return $this->currentNumber;
    }

    public function formatPrice($value)
    {
        return $this->_priceCurrency->convertAndFormat($value);
    }

    public function convertPrice($value)
    {
        return $this->_priceCurrency->convert($value);
    }

    public function getWishlistCollection(Wishlist $wishlist)
    {
        $collection = $this->_productCollection
            ->setStoreId($wishlist->getStore()->getId())
            ->addWishlistFilter($wishlist)
            ->addWishListSortOrder();

        return $collection;
    }

    public function saveUrlRewrite($storeCode = null)
    {
        if (!$storeCode) {
            $storeId = $this->_modelStoreManagerInterface->getDefaultStoreView()->getId();
        } else {
            $storeId = ObjectManager::getInstance()->create('Magento\Store\Model\Store')->loadConfig($storeCode)->getId();
        }

        $requestPath = $this->_helperConfig->getRouterName($storeId);
        $rewrite     = ObjectManager::getInstance()->create('Magento\UrlRewrite\Model\UrlRewrite')
            ->loadByIdPath(self::SECURED_CHECKOUT_URL_REWRITE_ID_PATH);
        if (!$rewrite || !$rewrite->getId()) {
            $rewrite->setIdPath(self::SECURED_CHECKOUT_URL_REWRITE_ID_PATH);
            $rewrite->setTargetPath('onestepcheckout');
        }
        $rewrite
            ->setRequestPath($requestPath);
        try {
            $rewrite->save();
        } catch (\Exception $e) {
        }

    }

    public function getHowToCheckoutUrl()
    {
        return $this->_urlBuilder->getBaseUrl(true) . 'how-to-checkout-our-store';
    }

    /**
     * Subscriber Newsletter
     *
     * @return Subscriber
     */
    public function getSubscriber()
    {
        return $this->_subscriber;
    }

    /**
     * @param $email
     * @return bool
     */
    public function isSubscribed($email)
    {
        $subscriber = $this->getSubscriber()->loadByEmail($email);
        if ($subscriber && $subscriber->getId()) {
            return true;
        }

        return false;
    }

}