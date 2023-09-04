define([
    'underscore',
    'jquery',
    'mage/template',
    'text!Aheadworks_Helpdesk2/template/ui/form/merge/ticket-confirm.html',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal'
], function (_, $, mageTemplate, ticketConfirmTemplate, malert) {
    "use strict";

    return {
        selectTicketsUrl: null,
        mergeTicketsUrl: null,
        mainTicketConfirm: {},

        /**
         * Set process urls
         *
         * @param selectTicketsUrl
         * @param mergeTicketsUrl
         */
        setProcessUrls(selectTicketsUrl, mergeTicketsUrl) {
            this.selectTicketsUrl = selectTicketsUrl;
            this.mergeTicketsUrl = mergeTicketsUrl;
        },

        /**
         * Show message
         *
         * @param title
         * @param content
         */
        showMessage: function (title, content) {
            malert({
                title: title,
                content: content,
                clickableOverlay: false,
                actions: {
                    always: function () {}
                }
            });
        },

        /**
         * Process tickets confirm
         *
         * @param mainTicketUid
         * @param mainTicketEntityId
         * @param selectedTicketsUid
         * @param selectTicketModal
         */
        processTicketsConfirm: function (
            mainTicketUid,
            mainTicketEntityId,
            selectedTicketsUid,
            selectTicketModal = null
        ) {
            var self = this;
            $.ajax({
                url: this.selectTicketsUrl,
                type: "POST",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    main_ticket_uid: mainTicketUid,
                    main_ticket_entity_id: mainTicketEntityId,
                    selected_tickets_uid: selectedTicketsUid,
                },
                complete: function (response) {
                    var result = response.responseJSON;
                    if (result.success) {
                        var responseData = result.data;
                        if (selectTicketModal) {
                            selectTicketModal.trigger('closeModal');
                        }
                        var warningNote = null;
                        if (responseData.hasOwnProperty('warning_note')) {
                            warningNote = responseData.warning_note;
                        }
                        self.mainTicketConfirm = responseData.main_ticket_info;
                        self.callTicketConfirmModal(
                            responseData.selected_tickets,
                            warningNote
                        );
                    } else {
                        self.showMessage($.mage.__('Warning'), result.message);
                    }
                },
                error: function (response) {
                    console.log(JSON.parse(JSON.stringify(response.responseText)));
                }
            });
        },

        /**
         * Call ticket confirm modal
         *
         * @param selectedTickets
         * @param warningNote
         */
        callTicketConfirmModal: function (selectedTickets, warningNote) {
            var self = this;
            var notice = this.getTicketConfirmNotice(selectedTickets)
            var modalHtml = mageTemplate(ticketConfirmTemplate, {
                selectedTickets: selectedTickets,
                mainTicket: this.mainTicketConfirm,
                notice: notice,
                warningNote: warningNote
            });
            $('.merge-ticket-form').remove();
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []
            }).trigger('openModal');
            if (warningNote) {
                $('#merge-warning-note').css('display','block');
            }
            this.setDefaultComments(this.mainTicketConfirm.comment);
            $('[data-button-tm-confirm-id="' + this.mainTicketConfirm.entity_id + '"]').click(function () {
                self.processTicketsMerge(selectedTickets, previewPopup);
            });
        },

        /**
         * Set default comments
         *
         * @param mainTicketComment
         */
        setDefaultComments: function (mainTicketComment) {
            var lines = mainTicketComment.split('\r\n');
            var resultComment = '';
            lines.forEach(function (line) {
                resultComment = resultComment + line + '\r\n';
            });
            $('#ticket_comment-' + this.mainTicketConfirm.entity_id).val(resultComment);
        },

        /**
         * Process tickets merge
         *
         * @param selectedTickets
         * @param confirmTicketModal
         */
        processTicketsMerge: function (selectedTickets, confirmTicketModal) {
            var mergeData = this.collectTicketsMergeData(selectedTickets);
            $.ajax({
                url: this.mergeTicketsUrl,
                type: "POST",
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    merge_data: mergeData
                },
                complete: function (response) {
                    var result = response.responseJSON;
                    if (result.success) {
                        confirmTicketModal.trigger('closeModal');
                    }
                },
                error: function (response) {
                    console.log(JSON.parse(JSON.stringify(response.responseText)));
                }
            }).always(function () {
                location.reload();
            });
        },

        /**
         * Collect tickets merge data
         *
         * @param selectedTickets
         * @returns {{main_ticket_data: {comment: (*|define.amd.jQuery|string), entity_id: *, is_requested_see_comment: (*|define.amd.jQuery)}, merge_tickets: *[]}}
         */
        collectTicketsMergeData: function (selectedTickets) {
            var mergeData = [];
            selectedTickets.forEach(function (ticket) {
                var comment = $('#ticket_comment-' + ticket.entity_id).val();
                var isSeeComment = $('#is_requested_see_comment-' + ticket.entity_id).is(":checked");
                mergeData.push({
                    entity_id: ticket.entity_id,
                    comment: comment,
                    is_requested_see_comment: isSeeComment
                });
            });
            var mainTicketEntityId = this.mainTicketConfirm.entity_id;
            var mainTicketComment = $('#ticket_comment-' + mainTicketEntityId).val();
            var isMainTicketSeeComment = $('#is_requested_see_comment-' + mainTicketEntityId).is(":checked");

            return {
                main_ticket_data: {
                    entity_id: mainTicketEntityId,
                    comment: mainTicketComment,
                    is_requested_see_comment: isMainTicketSeeComment
                },
                merge_tickets: mergeData
            };
        },

        /**
         * Get ticket confirm notice
         *
         * @param selectedTickets
         * @returns {*}
         */
        getTicketConfirmNotice: function (selectedTickets) {
            var notice = $.mage.__('You are about to merge tickets ');
            selectedTickets.forEach(function (ticket, idx, selectedTickets) {
                if (idx === selectedTickets.length - 1) {
                    notice = notice + ticket.uid;
                } else {
                    notice = notice + ticket.uid + ', ';
                }
            });

            return notice + $.mage.__(' into ticket ') + this.mainTicketConfirm.uid;
        },
    }
});
