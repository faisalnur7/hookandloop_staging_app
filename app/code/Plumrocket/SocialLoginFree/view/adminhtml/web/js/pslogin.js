/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

require([
    'jquery',
    'jquery/ui',
    'mage/adminhtml/events'
], function ($) {
    // 'use strict';

    $(function () {
        // Sortable.
        $('ul#sortable-visible').sortable({
            connectWith: "ul",
            receive: function (event, ui) {
                ui.item.attr('id', ui.item.attr('id').replace(ui.sender.data('list'), $(this).data('list')));
            },
            update: function () {
                $('#psloginfree_buttons_sortable').val($('#sortable-visible').sortable('serialize'));
            },
            stop: function () {
                if (this.id === 'sortable-visible' && $('#'+ this.id +' li').length < 1) {
                    alert('Sorry, "Visible Buttons" list can not be empty');
                    $(this).sortable('cancel');
                }
            }
        })
        .disableSelection();

        if ($('#psloginfree_buttons_sortable_drag_and_drop').css('display') !== 'none') {
            const $useDefaultCheckbox = $('#psloginfree_general_sortable_inherit');
            if ($useDefaultCheckbox.length) {
                $useDefaultCheckbox.on('change', function () {
                    const $sortLists = $('ul#sortable-visible, ul#sortable-hidden');
                    if ($(this).is(':checked')) {
                        $sortLists.sortable({disabled: true});
                    } else {
                        $sortLists.sortable({disabled: false});
                    }
                }).change();
            }
        } else {
            $('#row_psloginfree_buttons_sortable').hide();
        }

        // Share Url.
        $('#psloginfree_share_page').find('option[value=__invitationsoff__], option[value=__none__]').prop('disabled', true);

        // Callback URL.
        $('.psloginfree-callbackurl-autofocus').on('focus click', function () {
            $(this).select();
        })
        .each(function (n, item) {
            const $item = $(item);
            if ($item.val().indexOf('http://') >= 0) {
                $item.next('p.note').find('span span').css('color', 'red');
            }
        });


        // Show/hide supported networks
        const proNetworksWrapper = document.querySelector('.pro-networks');

        if (proNetworksWrapper) {
            const networkGrid = proNetworksWrapper.querySelector('.pro-networks_grid');
            let isOpen = false;

            proNetworksWrapper.addEventListener('click', (e) => {
                const targetEl = e.target;

                if (targetEl.closest('.pro-networks_btn') === targetEl) {
                    e.preventDefault();

                    if (isOpen) {
                        networkGrid.style.height = null;
                        targetEl.innerText = 'show more'
                    } else {
                        networkGrid.style.height = networkGrid.scrollHeight + "px";
                        targetEl.innerText = 'show less'
                    }

                    isOpen = !isOpen;
                }
            })
        }
    });
});
