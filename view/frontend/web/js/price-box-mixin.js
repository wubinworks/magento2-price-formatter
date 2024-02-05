/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    return function (priceBox) {

        $.widget('mage.priceBox', priceBox, {

            /**
             * If PriceFormatter is enabled, change requiredPrecision and priceTemplate.
             */
            reloadPrice: function reDrawPrices() {
                var tmp;

                if (this.options.priceConfig.priceFormat.pattern.enabled) {
                    tmp = this.options.priceConfig.priceFormat.pattern;
                    this.options.priceConfig.priceFormat.requiredPrecision = tmp.requiredPrecision;
                    this.options.priceConfig.priceFormat.pattern = tmp.pattern;
                    this.options.priceTemplate = '<span class="price"><%= data.formatted %></span>';
                }

                this._super();
            }
        });

        return $.mage.priceBox;
    };
});
