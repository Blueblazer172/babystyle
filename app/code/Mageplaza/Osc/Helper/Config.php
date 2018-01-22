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

use Magento\CheckoutAgreements\Model\AgreementsProvider;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * @var HelperData
     */
    protected $_agreementProvider;
    /**
     * "Enable One Step Checkout" from system config
     */
    const GENERAL_IS_ENABLED = 'osc/general/is_enabled';
    /**
     * Use secure in frontend
     */
    const XML_PATH_SECURE_IN_FRONTEND = 'web/secure/use_in_frontend';
    /**
     * General configuaration path
     */
    const GENERAL_CONFIGUARATION = 'osc/general/';
    /**
     * Display configuaration path
     */
    const DISPLAY_CONFIGUARATION = 'osc/display_configuration/';
    /**
     * Design configuaration path
     */
    const DESIGN_CONFIGUARATION = 'osc/design_configuration/';
    /**
     * Term and condition config path
     */
    const TERM_AND_CONDITION_CONFIGUARATION = 'osc/terms_conditions/';
    const TERM_AND_CONDITION_ID = 'mc_osc_term';

    /**
     * @param Context       $context
     * @param HelperData    $agreementProvider
     */
    public function __construct(Context $context,
                                AgreementsProvider $agreementProvider)
    {
        $this->_agreementProvider = $agreementProvider;

        parent::__construct($context);
    }

    /**
     * get Magento Config Value
     *
     * @param      $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        $isModuleEnabled       = $this->isModuleEnabled();
        $isModuleOutputEnabled = $this->isModuleOutputEnabled();

        return $isModuleOutputEnabled && $isModuleEnabled && $this->getConfigValue(self::GENERAL_IS_ENABLED, $store);
    }

    public function isModuleEnabled()
    {
        $moduleName = "Mageplaza_Osc";

        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * @return mixed
     */
    public function isSecure($store = null)
    {
        $isSecure = $this->getConfigValue(self::XML_PATH_SECURE_IN_FRONTEND, $store);

        return $isSecure;
    }

    /**
     * get general config by code
     *
     * @param      $code
     * @param null $store
     * @return mixed
     */
    public function getGeneralConfig($code, $store = null)
    {
        return $this->getConfigValue(self::GENERAL_CONFIGUARATION . $code, $store);
    }

    public function getRouterName($store = null)
    {
        return $this->getGeneralConfig('router_name', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function allowGuestCheckout($store = null)
    {
        return $this->getGeneralConfig('allow_guest_checkout', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function allowShipToDifferent($store = null)
    {
        return $this->getGeneralConfig('allow_ship_to_defferent_address', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getCheckoutTitle($store = null)
    {
        return $this->getGeneralConfig('title', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getCheckoutDescription($store = null)
    {
        return $this->getGeneralConfig('description', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getDefaultPaymentMethod($store = null)
    {
        return $this->getGeneralConfig('default_payment_method', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getDefaultShippingMethod($store = null)
    {
        return $this->getGeneralConfig('default_shipping_method', $store);
    }

    /**
     * @param $carrierCode
     * @return mixed
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->getConfigValue('carriers/' . $carrierCode . '/title')) {
            return $name;
        }

        return $carrierCode;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getDefaultCountryId($store = null)
    {
        return $this->getGeneralConfig('default_country_id', $store);
    }

    public function isIntegratedSocialLogin($store = null)
    {
        return $this->getGeneralConfig('integrated_sociallogin', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isAutoDetectedAddress($store = null)
    {

        return $this->getGeneralConfig('auto_detect_address', $store) == 'google';
    }

    public function  isEnabledAddressByCountry($store = null)
    {
        return $this->getGeneralConfig('auto_detect_address', $store) == 'pca';
    }

    public function getGoogleApiKey($store = null)
    {
        return $this->getGeneralConfig('google_api_key', $store);
    }

    public function getGoogleSpecificCountry($store = null)
    {
        return $this->getGeneralConfig('google_specific_country', $store);
    }

    public function  getPcaWebsiteKey($store = null)
    {
        return $this->getGeneralConfig('pca_website_key', $store);
    }

    public function  getPcaCountryLookup($store = null)
    {
        return $this->getGeneralConfig('pca_country_lookup', $store);
    }

    public function getDisplayConfig($code, $store = null)
    {
        return $this->getConfigValue(self::DISPLAY_CONFIGUARATION . $code, $store);
    }

    public function isEnabledReviewCartSection($store = null)
    {
        return $this->getDisplayConfig('is_enabled_review_cart_section', $store);
    }

    public function isShowProductImage($store = null)
    {
        return $this->getDisplayConfig('is_show_product_image', $store);
    }

    public function getProductImageWidth($store = null)
    {
        return $this->getDisplayConfig('product_image_width', $store);
    }

    public function getProductImageHeight($store = null)
    {
        return $this->getDisplayConfig('product_image_height', $store);
    }

    public function isShowEditCartLink($store = null)
    {
        return $this->getDisplayConfig('is_show_edit_cart_link', $store);
    }

    public function disableShippingAddress($store = null)
    {
        return $this->getDisplayConfig('is_disabled_shipping_address', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isCoupon($store = null)
    {
        return $this->getDisplayConfig('is_enabled_coupon', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabledCommments($store = null)
    {
        return $this->getDisplayConfig('is_enabled_comments', $store);
    }

    public function isEnabledGiftMessage($store = null)
    {
        return $this->getDisplayConfig('is_enabled_giftmessage', $store);
    }

    public function isEnabledGiftWrap($store = null)
    {
        return $this->getDisplayConfig('is_enabled_giftwrap', $store);
    }

    public function getGiftWrapType($store = null)
    {
        return $this->getDisplayConfig('giftwrap_type', $store);
    }

    public function getOrderGiftwrapAmount($store = null)
    {
        return $this->getDisplayConfig('giftwrap_amount', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isNewsletter($store = null)
    {
        return $this->getDisplayConfig('is_enabled_newsletter', $store);
    }

    public function isSubscribedByDefault($store = null)
    {
        return $this->getDisplayConfig('is_checked_newsletter', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isRelatedProducts($store = null)
    {
        return $this->getDisplayConfig('is_enabled_related_products', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getBlockBelowBillingAddress($store = null)
    {
        return $this->getDisplayConfig('display_block_below_billing_address', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getBlockBelowShippingAddress($store = null)
    {
        return $this->getDisplayConfig('display_block_below_shipping_address', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getBlockBelowShippingMethod($store = null)
    {
        return $this->getDisplayConfig('display_block_below_shipping_method', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getBlockBelowPaymentMethod($store = null)
    {
        return $this->getDisplayConfig('display_block_below_payment_method', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isApplyCouponButton($store = null)
    {
        return $this->getDisplayConfig('display_apply_coupon_button', $store);
    }


    public function showGrandTotal($store = null)
    {
        return $this->getDisplayConfig('is_show_grand_total', $store);
    }

    public function isEnabledMorphEffect($store = null)
    {
        return $this->getDisplayConfig('is_enabled_morph_effect', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getDesignConfig($code, $store = null)
    {
        return $this->getConfigValue(self::DESIGN_CONFIGUARATION . $code, $store);
    }

    public function isShowNumbering($store = null)
    {
        return $this->getDesignConfig('is_show_numbering', $store);
    }


    public function getPopupEffect($store = null)
    {
        return $this->getDesignConfig('popup_effect', $store);
    }

    public function getHeadingStyle($store = null)
    {
        return $this->getDesignConfig('heading_style', $store);

    }

    /**
     * get layout tempate: 1 or 2 or 3 columns
     *
     * @param null $store
     * @return string
     */
    public function getLayoutTemplate($store = null)
    {
        $templateFile = 'checkout-' . $this->getDesignConfig('page_layout', $store) . '.phtml';

        return $templateFile;
    }

    public function getLoginPopupWidth($store = null)
    {
        return $this->getDesignConfig('login_popup_width', $store);
    }

    public function getLoginPopupHeight($store = null)
    {
        return $this->getDesignConfig('login_popup_height', $store);
    }

    /**
     * @param $color
     * @return string
     */
    public function mappingColor($color)
    {
        if ($color == 'orange')
            return '#F39801';
        if ($color == 'green')
            return '#B6CE5E';
        if ($color == 'black')
            return '#4D4D4D';
        if ($color == 'blue')
            return '#3398CC';
        if ($color == 'darkblue')
            return '#004BA0';
        if ($color == 'pink')
            return '#E13B91';
        if ($color == 'red')
            return '#E10E03';
        if ($color == 'violet')
            return '#B962d5';

        return $color;
    }

    /**
     * @param null $store
     * @return mixed|string
     */
    public function getStyleColor($store = null)
    {
        $style_config = $this->getDesignConfig('style_color', $store);
        if ($style_config != 'custom')
            return $this->mappingColor($style_config);

        return $this->getDesignConfig('style_custom');
    }

    public function getHeadingTextColor($store = null)
    {
        return $this->getDesignConfig('style_heading_custom', $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getPlaceOrderColor($store = null)
    {
        return $this->getDesignConfig('place_order_color', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getCustomCss($store = null)
    {
        return $this->getDesignConfig('custom_css', $store);
    }

    /**
     * @param      $code
     * @param null $store
     * @return mixed
     */
    public function getTermAndConditionConfig($code, $store = null)
    {
        return $this->getConfigValue(self::TERM_AND_CONDITION_CONFIGUARATION . $code, $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabledTerm($store = null)
    {
        return $this->getTermAndConditionConfig('enabled_terms', $store);
    }

    public function isRequiredReadTerm($store = null)
    {
        return $this->getTermAndConditionConfig('require_read', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getTermTitle($store = null)
    {
        return $this->getTermAndConditionConfig('term_title', $store);
    }

    public function getTermCheckboxText($store = null)
    {
        return $this->getTermAndConditionConfig('checkbox_text', $store);
    }

    public function getRequiredAgreementIds()
    {
        $agreements          = [];
        $agreements[]        = self::TERM_AND_CONDITION_ID;
        $defaultAgreementIds = $this->_agreementProvider->getRequiredAgreementIds();
        $agreements          = array_merge($agreements, $defaultAgreementIds);

        return $agreements;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getTermContent($store = null)
    {
        return $this->getTermAndConditionConfig('term_html', $store);
    }

}