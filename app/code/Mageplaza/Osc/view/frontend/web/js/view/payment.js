/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        "underscore",
        'uiComponent',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Mageplaza_Osc/js/action/get-payment-information',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Mageplaza_Osc/js/model/payment/loader'
    ],
    function ($,
              _,
              Component,
              ko,
              quote,
              stepNavigator,
              paymentService,
              methodConverter,
              getPaymentInformation,
              checkoutDataResolver,
              paymentLoader) {
        'use strict';

        /** Set payment methods to collection */
        paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));

        return Component.extend({
            defaults: {
                template: 'Mageplaza_Osc/payment',
                activeMethod: ''
            },
            isVisible: ko.observable(quote.isVirtual()),
            quoteIsVirtual: quote.isVirtual(),
            isPaymentMethodsAvailable: ko.computed(function () {
                return paymentService.getAvailablePaymentMethods().length > 0;
            }),

            initialize: function () {
                this._super();
                checkoutDataResolver.resolvePaymentMethod();
                this.navigate();
                window.payment = this;
                window.paymentList = this.elems();
                quote.paymentMethod(window.secureCheckoutConfig.defaultPaymentMethod);
                quote.paymentMethod.subscribe(function (paymentMethod) {

                });

                return this;
            },
            afterRender: function () {
                /*Input code here*/
            },
            navigate: function () {
                var self = this;
                paymentLoader.startLoader();
                getPaymentInformation().done(function () {
                    self.isVisible(true);
                    paymentLoader.stopLoader();
                });
            },

            getFormKey: function () {
                return window.checkoutConfig.formKey;
            }
        });
    }
);
