/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Mageplaza_Osc/js/view/payment/default',
        'ko',
        'Magento_Paypal/js/model/iframe',
        'Mageplaza_Osc/js/model/full-screen-loader',
        'Mageplaza_Osc/js/modal/modal',
        'Magento_Ui/js/model/messageList'
    ],
    function (Component, ko, iframe, fullScreenLoader, modal, messageList) {
        'use strict';

        var popUp = null;
        return Component.extend({
            defaults: {
                template: 'Mageplaza_Osc/payment/iframe-methods',
                paymentReady: false
            },
            redirectAfterPlaceOrder: false,
            isInAction: iframe.isInAction,
            initObservable: function () {
                this._super()
                    .observe('paymentReady');

                return this;
            },
            isPaymentReady: function () {
                return this.paymentReady();
            },
            /**
             * Get action url for payment method iframe.
             * @returns {String}
             */
            getActionUrl: function () {
                return this.isInAction() ? window.checkoutConfig.payment.paypalIframe.actionUrl[this.getCode()] : '';
            },
            /**
             * Places order in pending payment status.
             */
            placePendingPaymentOrder: function () {
                var self = this;
                this.afterPlaceOrder = function () {
                    self.getPopUp().openModal();
                    self.paymentReady(true);
                    ko.computed(function(){
                        if(!iframe.isInAction()){
                            this.getPopUp().closeModal();
                        }
                    }, self);
                };
                if (this.placeOrder()) {
                    this.isInAction(true);
                    // capture all click events
                    document.addEventListener('click', iframe.stopEventPropagation, true);
                }
            },

            getMessageList: function(){
                return messageList;
            },

            getPopUp: function () {
                if (!popUp) {
                    popUp = modal({}, $('paypal-iframe-' + this.getCode()));
                }

                return popUp;
            },

            /**
             * Hide loader when iframe is fully loaded.
             * @returns {void}
             */
            iframeLoaded: function() {
                fullScreenLoader.stopLoader();
            }
        });
    }
);
