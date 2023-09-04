/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */
define([
    'ko',
    'jquery',
    'underscore',
    'uiComponent',
    'mage/translate',
    'pslogin'
], function (ko, $, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Plumrocket_SocialLoginFree/buttons'
        },

        useCustomClickHandler: ko.observable(false),
        customClickHandler: null,
        /**
         * Example: [
         *     type: 'windowFunction',
         *     path: ['objectName', 'functionName']
         * ]
         */
        customClickHandlerConfig: null,
        separatorText: 'or',
        customSeparatorHtml: null,

        initialize: function () {
            this._super();
            this.initCustomClickHandler();
        },

        canShow: function () {
            return this.buttons.length;
        },

        canShowSeparator: function () {
            return this.canShow;
        },

        /**
         * @param {{networkCode: string, design: string}} button
         * @returns {string}
         */
        getButtonClass: function (button) {
            return 'pslogin-button ' + button.networkCode + ' ps-' + button.design;
        },

        getInnerContainerClass: function (button) {
            return 'pslogin-button-auto';
        },

        initCustomClickHandler: function () {
            if (! this.customClickHandlerConfig || ! this.customClickHandlerConfig.type) {
                return;
            }
            if ('windowFunction' === this.customClickHandlerConfig.type) {
                var handler = _.get(window, this.customClickHandlerConfig.path);
                if (typeof handler !== 'function') {
                    return;
                }
                this.customClickHandler = handler;
                this.useCustomClickHandler(true);
            }
        },

        onClick: function (button, $event) {
            if (typeof this.customClickHandler !== 'function') {
                return;
            }
            this.customClickHandler(button, $event.currentTarget);
        },
    });
});
