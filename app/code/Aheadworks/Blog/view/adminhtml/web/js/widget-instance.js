define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    $.widget('mage.widgetInstance', {
        options: {
            displayOnContainers: [],
            baseChooserUrl: '',
            selector: '.chooser_container[id*="{id}"] .widget-option-chooser',
            selectedItems : {}
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            this._bind();
        },

        _bind: function () {
            var self = this;

            $.each(this.options.displayOnContainers, function(key, container) {
                $(document).on('click', self.options.selector.replace('{id}', key), function(event) {
                    var id = $(event.target).parents(`.chooser_container[id*="${key}"]`).attr('id'),
                        additional = {
                        url: self.options.baseChooserUrl,
                        post_parameters: $H({'code': key, 'display_type': container.display_type})
                    };

                    WidgetInstance.displayEntityChooser(key, id, additional)
                })
            });

            Event.observe(document, 'item:changed', function(event){
                self.checkNode(event);
            });
        },

        /**
         * Check selected node
         */
        checkNode: function (event) {
            var element = $(event.memo.element),
                container = element.parents('.chooser_container'),
                selectionId = container.attr('id'),
                entitiesElm = container.find('input[type="text"].entities'),
                oldValue = entitiesElm.val();

            this.updateSelectedByOldValues(selectionId, oldValue)

            if (element.prop('checked')) {
                this.addItemToSelection(selectionId, element.val());
            } else {
                this.removeItemFromSelection(selectionId, element.val());
            }

            this.setSelectionItems(selectionId, entitiesElm);
        },

        /**
         * Set Selection Items
         */
        setSelectionItems(selectionId, element)
        {
            if (element && element.length) {
                element.val(this.options.selectedItems[selectionId].keys().join(','));
            }
        },

        /**
         * Update Selected By Old Values
         */
        updateSelectedByOldValues: function(selectionId, oldValue) {
            if (oldValue) {
                oldValue = oldValue.split(',');

                $.each(oldValue, function(key, value) {
                    value = value.trim();
                    this.addItemToSelection(selectionId, value);
                }.bind(this));
            }
        },

        /**
         * Add Item To Selection
         */
        addItemToSelection: function(groupId, item) {
            if (this.options.selectedItems[groupId] == undefined) {
                this.options.selectedItems[groupId] = $H({});
            }
            if (!isNaN(parseInt(item))) {
                this.options.selectedItems[groupId].set(item, 1);
            }
        },

        /**
         * Remove Item To Selection
         */
        removeItemFromSelection: function(groupId, item) {
            if (this.options.selectedItems[groupId] !== undefined) {
                this.options.selectedItems[groupId].unset(item);
            }
        },
    });

    return $.mage.widgetInstance;
});
