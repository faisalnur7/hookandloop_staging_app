define([
    'ko',
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'jquerytokenize'
], function (ko, $, Abstract) {
    'use strict';

    /**
     * Update value
     * @param {Object} viewModel
     * @param {String} value
     */
    function updateValue(viewModel, value) {
        viewModel.value(value);
    }

    /**
     * Preselect Values
     *
     * @param {Object} element
     * @param {Object} viewModel
     */
    function preselectValues(element, viewModel) {
        var selectedValues = viewModel.value();
        for (let option of element.options) {
            if (selectedValues.includes(option.value)) {
                option.selected = true;
            }
        }
    }

    ko.bindingHandlers.blogTags = {

        /**
         * Init binding
         * @param  {Object} element
         * @param  {Function} valueAccessor
         * @param  {Object} allBindings
         * @param  {Object} viewModel
         */
        init: function (element, valueAccessor, allBindings, viewModel) {
            if (valueAccessor()) {

                //magento 2.4.3 doesn't set option selected, but tokenizer need it
                preselectValues(element, viewModel);

                $('#' + element.id).tokenize({

                    /**
                     * Calls callback when event is triggered
                     * @param  {String} value
                     * @param  {String} text
                     * @param  {Object} tokenize
                     */
                    onAddToken: function (value, text, tokenize) {
                        updateValue(viewModel, tokenize.select.val());
                    },

                    /**
                     * Calls callback when event is triggered
                     * @param  {String} value
                     * @param  {Object} tokenize
                     */
                    onRemoveToken: function (value, tokenize) {
                        updateValue(viewModel, tokenize.select.val());
                    }
                });
            }
        }
    };

    return Abstract.extend({
        defaults: {
            template: 'Aheadworks_Blog/ui/form/element/tags'
        }
    });
});
