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

define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/billing-address',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Mageplaza_Osc/js/model/osc-data',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Mageplaza_Osc/js/model/address/auto-complete',
        'uiRegistry',
        'mage/translate',
        'rjsResolver'
    ],
    function ($,
              ko,
              Component,
              quote,
              checkoutData,
              oscData,
              createBillingAddress,
              selectBillingAddress,
              customer,
              setBillingAddressAction,
              addressConverter,
              additionalValidators,
              globalMessageList,
              checkoutDataResolver,
              addressAutoComplete,
              registry,
              $t,
              resolver) {
        'use strict';

        var observedElements = [];

        return Component.extend({
            defaults: {
                template: ''
            },
            quoteIsVirtual: quote.isVirtual(),

            canUseShippingAddress: ko.computed(function () {
                return !quote.isVirtual() && quote.shippingAddress() &&
                    quote.shippingAddress().canUseForBilling() && window.checkoutConfig.oscConfig.showBillingAddress;
            }),

            /**
             * @return {exports}
             */
            initialize: function () {
                var self = this;

                this._super();

                this.initFields();

                additionalValidators.registerValidator(this);

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var billingAddressData = checkoutData.getBillingAddressFromData();

                    if (billingAddressData) {
                        checkoutProvider.set(
                            'billingAddress',
                            $.extend({}, checkoutProvider.get('billingAddress'), billingAddressData)
                        );
                    }
                    checkoutProvider.on('billingAddress', function (billingAddressData) {
                        checkoutData.setBillingAddressFromData(billingAddressData);
                    });
                });

                quote.shippingAddress.subscribe(function (newAddress) {
                    if (self.isAddressSameAsShipping()) {
                        selectBillingAddress(newAddress);
                    }
                });

                resolver(this.afterResolveDocument.bind(this));

                return this;
            },

            afterResolveDocument: function () {
                this.saveBillingAddress();

                addressAutoComplete.register('billing');
            },

            /**
             * @return {Boolean}
             */
            useShippingAddress: function () {
                if (this.isAddressSameAsShipping()) {
                    selectBillingAddress(quote.shippingAddress());
                    checkoutData.setSelectedBillingAddress(null);
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                } else {
                    this.updateAddress();
                }

                return true;
            },

            onAddressChange: function (address) {
                this._super(address);

                if (!this.isAddressSameAsShipping()) {
                    this.updateAddress();
                }
            },

            /**
             * Update address action
             */
            updateAddress: function () {
                if (this.selectedAddress() && !this.isAddressFormVisible()) {
                    newBillingAddress = createBillingAddress(this.selectedAddress());
                    selectBillingAddress(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                } else {
                    var addressData = this.source.get(this.dataScopePrefix),
                        newBillingAddress;

                    if (customer.isLoggedIn() && !this.customerHasAddresses) {
                        this.saveInAddressBook(1);
                    }
                    addressData.save_in_address_book = this.saveInAddressBook() ? 1 : 0;
                    newBillingAddress = createBillingAddress(addressData);

                    // New address must be selected as a billing address
                    selectBillingAddress(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                    checkoutData.setNewCustomerBillingAddress(addressData);

                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                }
            },

            /**
             * Perform postponed binding for fieldset elements
             */
            initFields: function () {
                var self = this,
                    fieldsetName = 'checkout.steps.shipping-step.billingAddress.billing-address-fieldset';

                var elements = registry.async(fieldsetName)().elems();

                $.each(elements, function (index, elem) {
                    self.bindHandler(elem);
                });

                return this;
            },

            bindHandler: function (element) {
                var self = this;

                if (element.component.indexOf('/group') !== -1) {
                    $.each(element.elems(), function (index, elem) {
                        self.bindHandler(elem);
                    });
                } else {
                    element.on('value', this.saveBillingAddress.bind(this));
                    observedElements.push(element);
                }
            },

            saveBillingAddress: function () {
                if (!this.isAddressSameAsShipping()) {
                    if (!window.checkoutConfig.oscConfig.showBillingAddress) {
                        selectBillingAddress(quote.shippingAddress());
                    } else {
                        var addressFlat = addressConverter.formDataProviderToFlatData(
                            this.collectObservedData(),
                            'billingAddress'
                        );

                        selectBillingAddress(addressConverter.formAddressDataToQuoteAddress(addressFlat));
                    }
                }
            },

            /**
             * Collect observed fields data to object
             *
             * @returns {*}
             */
            collectObservedData: function () {
                var observedValues = {};

                $.each(observedElements, function (index, field) {
                    observedValues[field.dataScope] = field.value();
                });

                return observedValues;
            },

            validate: function () {
                if (this.isAddressSameAsShipping()) {
                    oscData.setData('same_as_shipping', true);
                    return true;
                }

                if (!this.isAddressFormVisible()) {
                    return true;
                }

                this.source.set('params.invalid', false);
                this.source.trigger('billingAddress.data.validate');

                if (this.source.get('billingAddress.custom_attributes')) {
                    this.source.trigger('billingAddress.custom_attributes.data.validate');
                }

                oscData.setData('same_as_shipping', false);
                return !this.source.get('params.invalid');
            },
            getAddressTemplate: function () {
                return 'Mageplaza_Osc/container/address/billing-address';
            }
        });
    }
);
