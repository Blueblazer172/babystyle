/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    $.widget("mageplaza.modal", modal, {
        options: {
            clickableOverlay: false,
            buttons: {},
            modalCloseBtn: ''
        },

        keyEventHandlers: {
            tabKey: function () {
                if (document.activeElement === this.modal[0]) {
                    this._setFocus('start');
                }
            }
        }
    });

    return $.mageplaza.modal;
});
