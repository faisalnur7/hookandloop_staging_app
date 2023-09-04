<?php

namespace Ravedigital\Custom\Block\Product\View\Options;

// use Magento\Catalog\Block\Product\View\Options\AbstractOptions as MagentoAbstractOptions;

// die("shub1");
class AbstractOptionsSelect extends \Magento\Catalog\Block\Product\View\Options\AbstractOptions
{
    protected function _formatPrice($value, $flag = true)
    {
        // die("shub");
        // Your custom code here
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;

        $customOptionPrice = $this->getProduct()->getPriceInfo()->getPrice('custom_option_price');
        $isPercent = (bool) $value['is_percent'];

        $context = [CustomOptionPriceInterface::CONFIGURATION_OPTION_FLAG => true];
        $optionAmount = $isPercent
            ? $this->calculator->getAmount(
                $this->priceCurrency->roundPrice($value['pricing_value']),
                $this->getProduct(),
                null,
                $context
            ) : $customOptionPrice->getCustomAmount($value['pricing_value'], null, $context);
            $priceStr = '';
            $block = $this->getLayout()->getBlock('product.price.render.default');
            if ($block) {
                $priceStr = $block->renderAmount(
                    $optionAmount,
                    $customOptionPrice,
                    $this->getProduct()
                );
            } else {
                $priceStr = 'Block not found';
            }

        if ($flag) {
            $priceStr = '<span class="price-notice">' . $priceStr . '</span>';
        }
        return $priceStr;
    }
}