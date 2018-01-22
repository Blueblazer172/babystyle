define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/model/payment/additional-validators',
        'ko'
    ],
    function ($, ccFormComponent, additionalValidators, ko) {
        'use strict';

        return ccFormComponent.extend({
            defaults: {
                template: 'Magenest_Moneris/payment/moneris-direct-form',
                active: false,
                scriptLoaded: false,
                imports: {
                    onActiveChange: 'active'
                }
            },
            placeOrderHandler: null,
            validateHandler: null,

            initObservable: function () {
                this._super()
                    .observe('active');
                return this;
            },

            context: function () {
                return this;
            },

            getConfig: function (key) {
                if (window.checkoutConfig.payment[this.getCode()][key] != undefined) {
                    return window.checkoutConfig.payment[this.getCode()][key];
                }
                return null;
            },

            hasVerification: function () {
                if (this.getConfig('cvd') != null && this.getConfig('cvd') == 1) {
                    return true;
                }
                return false;
            },

            /**
             * @returns {Boolean}
             */
            isShowLegend: function () {
                return true;
            },

            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },

            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },


            getCode: function () {
                return 'moneris';
            },


            isActive: function () {
                var active = this.getCode() === this.isChecked();

                this.active(active);

                return active;
            },

            placeOrder: function () {
                if (this.validateHandler() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    this._super();
                }
            }
        });
    }
);
