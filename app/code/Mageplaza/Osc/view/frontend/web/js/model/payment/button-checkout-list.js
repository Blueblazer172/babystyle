/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        "underscore",
        'mage/mage',
        'jquery/ui',
        'mage/mage'
    ],
    function ($, _) {
        'use strict';
        var buttons = {};

        return {
            /**
             * Register unique validator
             *
             * @param form
             */
            registerButton: function (code) {
                buttons[code] = 'payment_btn_' + code;
            },

            /**
             * Register unique validator
             *
             * @param validator
             */
            clickButton: function (code) {
                var button = $('#' + buttons[code]);
                if(!button.length){
                    button = $("#one-step-checkout-payment-methods .payment-method._active button.action.primary.checkout").first();
                }

                button.click();
            }
        };
    }
);
