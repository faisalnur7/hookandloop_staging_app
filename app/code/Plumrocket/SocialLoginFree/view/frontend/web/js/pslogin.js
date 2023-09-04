/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

require([
    'jquery',
], function (pjQuery) {
    'use strict';

    pjQuery(document).ready(function () {

        // Show/Hide button.
        pjQuery(document).on('click', '.pslogin-showmore-button', function () {
            var $buttons = pjQuery(this).parents('div.pslogin-buttons');
            $buttons.find('.pslogin-hidden').fadeToggle(275);
            pjQuery(this).parents('div.pslogin-showmore').hide();
        });

        pjQuery(document).on('click', '.pslogin-button-click', function () {
            var $this = pjQuery(this);
            if ($this.data('useCustomClickHandler')) {
                return true;
            }
            psLogin($this.data('href'), $this.data('width'), $this.data('height'), $this.data('openOnPage'));
            return false;
        });

        // Fake email message.
        pjQuery('.pslogin-fake-email-message .close-message').on('click', function () {
            pjQuery(this).parent().hide();
        });

    });


    window.psLogin = function (href, width, height, openOnPage) {
        if (openOnPage) {
            require(
                ['Magento_Customer/js/customer-data'],
                function (customerData) {
                    customerData.invalidate(['*']);
                    window.location.href = href;
                }
            );
            return;
        }

        var win = null;
        if (!width) {
            width = 650;
        }

        if (!height) {
            height = 350;
        }

        var left = parseInt((pjQuery(window).width() - width) / 2);
        var top = parseInt((pjQuery(window).height() - height) / 2);

        var params = [
            'resizable=yes',
            'scrollbars=no',
            'toolbar=no',
            'menubar=no',
            'location=no',
            'directories=no',
            'status=yes',
            'width=' + width,
            'height=' + height,
            'left=' + left,
            'top=' + top
        ];

        // win = window.open('#', 'pslogin_popup', params.join(','));
        if (href) {
            win = window.open(href, 'pslogin_popup', params.join(','));
            win.focus();

            pjQuery(win.document).ready(function () {

                var loaderText = 'Loading...';
                var html = '<!DOCTYPE html><html style="height: 100%;"><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>' + loaderText + '</title></head>';
                html += '<body style="height: 100%; margin: 0; padding: 0;">';
                html += '<div style="text-align: center; height: 100%;"><div id="loader" style="top: 50%; position: relative; margin-top: -50px; color: #646464; height:25px; font-size: 25px; text-align: center; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">' + loaderText + '</div></div>';
                html += '</body></html>';

                pjQuery(win.document).contents().html(html);
            });

        } else {
            alert('The Login Application was not configured correctly. If your are the admin of store: Please activate “Enable Logging” in Magento Login Extension and try again to see error details.');
        }
        return false;
    }

});
