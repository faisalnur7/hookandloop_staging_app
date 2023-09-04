<?php

/**
 * *
 * @author DCKAP Team
 * @copyright Copyright (c) 2018 DCKAP (https://www.dckap.com)
 * @package Dckap_CustomFields
 */

namespace Dckap\CustomFields\Plugin\Checkout;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
/**
 * Class LayoutProcessorPlugin
 * @package Dckap\CustomFields\Plugin\Checkout
 */
//class LayoutProcessorPlugin {
class LayoutProcessorPlugin {

     protected $scopeConfig;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        array  $jsLayout
    ) { 
// die();
        $validation['required-entry'] = true;
        $validateaccount['required-entry'] = true;
        $validateaccount['validate-account'] = true;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-options']['children']['shipping_options_method']= [
            'component' => "Magento_Ui/js/form/element/select",
            'config' => [
                'component' => 'Dckap_CustomFields/js/view/form/element/selectship',
                'customScope' => 'shippingAddress.shippingoptions',
                'template' => 'ui/form/field',
                //'elementTmpl' => "ui/form/element/select",
                'elementTmpl' => 'Dckap_CustomFields/form/element/selectship',
                'id' => "shipping_options_method"
            ],
            'dataScope' => 'shippingAddress.shipping_option_field.shipping_options_method',
            'label' => "Shipping Method",
            'provider' => 'checkoutProvider',
            'visible' => true,
            'caption' => 'Please select',
            'options' => $this->getMethodOptions(),
            'validation' => $validation,
            'sortOrder' => 2,
            'id' => 'shipping_option_field[shipping_options_method]'
                ];
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-options']['children']['shipping_options_service'] = [
                'component' => "Magento_Ui/js/form/element/select",
                'config' => [
                'customScope' => 'shippingAddress.shippingoptions',
                'template' => 'ui/form/field',
                'elementTmpl' => "ui/form/element/select",
                'id' => "shipping_options_service"
                ],
                'dataScope' => 'shippingAddress.shipping_option_field.shipping_options_service',
                'label' => "Shipping Service",
                'provider' => 'checkoutProvider',
                'visible' => true,
                'caption' => 'Please select',
            // 'options' => $this->getSelectedMethodOption(),
                'validation' => $validation,
                'sortOrder' => 3,
                'id' => 'shipping_option_field[shipping_options_service]'
                ];
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-options']['children']['shipping_options_account_number'] = [
                'component' => "Magento_Ui/js/form/element/abstract",
                'config' => [
                'customScope' => 'shippingAddress.shippingoptions',
                'template' => 'ui/form/field',
                'elementTmpl' => "ui/form/element/input",
                'id' => "shipping_options_account_number"
                ],
                'dataScope' => 'shippingAddress.shipping_option_field.shipping_options_account_number',
                'label' => "Account Number",
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => $validation,
                'sortOrder' => 4,
                'id' => 'shipping_option_field[shipping_options_account_number]'
                ];

                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-options']['children']['shipping_options_account_zip_codes'] = [
                'component' => "Magento_Ui/js/form/element/abstract",
                'config' => [
                'customScope' => 'shippingAddress.shippingoptions',
                'template' => 'ui/form/field',
                'elementTmpl' => "ui/form/element/input",
                'id' => "shipping_options_account_zip_codes"
                ],
                'dataScope' => 'shippingAddress.shipping_option_field.shipping_options_account_zip_codes',
                'label' => "Account Zip Code",
                'provider' => 'checkoutProvider',
                'visible' => true,
                'sortOrder' => 5,
                'id' => 'shipping_option_field[shipping_options_account_zip_codes]'
                ];
        // Additional validation
        // Shipping Address
            return $jsLayout;

    }
   

    protected function getMethodOptions()
    {
        $items = [];
        $isFedex = $this->scopeConfig->getValue('carriers/shippingoptions/fedex_active');
        $isUps = $this->scopeConfig->getValue('carriers/shippingoptions/ups_active');
        $isDhl = $this->scopeConfig->getValue('carriers/shippingoptions/dhl_active');

        $key =1;
        if ($isFedex == 1) {
            $fedexMethod = 'Fedex';
            $items[$key]["value"] = strtolower($fedexMethod);
            $items[$key]["label"] = $fedexMethod;
            $key++;
        }
        if ($isUps == 1) {
            $upsMethod = 'UPS';
            $items[$key]["value"] = strtolower($upsMethod);
            $items[$key]["label"] = $upsMethod;
            $key++;
        }
        if ($isDhl == 1) {
            $dhlMethod = 'DHL';
            $items[$key]["value"] = strtolower($dhlMethod);
            $items[$key]["label"] = $dhlMethod;
            $key++;
        }
        return $items;
    }

    protected function getServiceOptions()
    {
        $items = [];

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/service_option');
        $methods = explode(',', $shippingOptionData);
        foreach ($methods as $key => $method) {
            $method = str_replace('"', '', $method);
            $items[$key+1]["value"] = strtolower($method);
            $items[$key+1]["label"] = $method;
        }
        return $items;
    }

    protected function getDhlServiceOptions()
    {
        $items = [];

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/dhl_service_option');
        $methods = explode(',', $shippingOptionData);
        foreach ($methods as $key => $method) {
            $method = str_replace('"', '', $method);
            $items[$key+1]["value"] = strtolower($method);
            $items[$key+1]["label"] = $method;
        }
        return $items;
    }

    protected function getFedexServiceOptions()
    {
        $items = [];

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/fedex_service_option');
        $methods = explode(',', $shippingOptionData);
        foreach ($methods as $key => $method) {
            $method = str_replace('"', '', $method);
            $items[$key+1]["value"] = strtolower($method);
            $items[$key+1]["label"] = $method;
        }
        return $items;
    }

    protected function getUpsServiceOptions()
    {
        $items = [];

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/ups_service_option');
        $methods = explode(',', $shippingOptionData);
        foreach ($methods as $key => $method) {
            $method = str_replace('"', '', $method);
            $items[$key+1]["value"] = strtolower($method);
            $items[$key+1]["label"] = $method;
        }
        return $items;
    }

    protected function getSelectedMethodOption()
    {
        return $this->getUpsServiceOptions();
    }
}
