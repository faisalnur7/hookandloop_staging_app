define([
    'underscore',
    'uiCollection',
    'mageUtils',
    'uiLayout'
], function (_, uiCollection, utils, layout) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            visible: true,
            valueFieldName: 'value',
            imports: {
                valueOptions: '${ $.provider }:value_mapper.conditions.value_options',
                currentCondition: '${ $.conditionProvider }:value'
            },
            listens: {
                currentCondition: 'onConditionChange'
            },
            valueElementTemplate: {
                text: {
                    component: 'Magento_Ui/js/form/element/abstract',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    validation: {
                        'required-entry': true
                    }
                },
                select: {
                    component: 'Magento_Ui/js/form/element/select',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    validation: {
                        'required-entry': true
                    }
                },
                multiselect: {
                    component: 'Magento_Ui/js/form/element/multiselect',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    validation: {
                        'required-entry': true
                    }
                },
                textarea: {
                    component: 'Magento_Ui/js/form/element/textarea',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    validation: {
                        'required-entry': true
                    }
                },
                boolean: {
                    component: 'Magento_Ui/js/form/element/single-checkbox',
                    template: 'ui/form/field',
                    provider: '${ $.provider }',
                    prefer: 'toggle',
                    valueMap: {
                        'true': '1',
                        'false': '0'
                    },
                    default: '0'
                },
                product_sku: {
                   component: 'Aheadworks_Helpdesk2/js/ui/form/element/automation/sku-select',
                       template: 'Aheadworks_Helpdesk2/ui/form/element/sku-select',
                       provider: '${ $.provider }',
                       selectionsProvider: '${ $.selectionsProvider }',
                       productProvider: '${ $.productProvider }',
                       validation: {
                           'required-entry': true
                       }
               },
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();
            this.observe('visible');
            return this;
        },

        /**
         * On condition change handler
         *
         * @param {String} condition
         */
        onConditionChange: function (condition) {
            var valueConfig = this.valueOptions[condition],
                type,
                option;

            this._removeAllElements();
            if (valueConfig) {
                type = this.valueElementTemplate[valueConfig.type];
                option = {
                    options: valueConfig.options || {},
                    name: Math.random() + this.valueFieldName,
                    parent: this.name,
                    dataScope: this.valueFieldName,
                    labelVisible: false,
                    selectionsProvider: this.selectionsProvider,
                    productProvider: this.productProvider,
                    additionalClasses: 'column value'
                };

                layout([utils.extend({}, type, option)]);
            }
        },

        /**
         * Remove all elements
         *
         * @private
         */
        _removeAllElements: function () {
            if (_.size(this.elems()) > 0) {
                this.source.remove(this.dataScope + '.' + this.valueFieldName);
                this.destroyChildren();
            }
        }
    });
});
