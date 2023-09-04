<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Model\Quote\Quote\Address\RateResult;

/**
 * Fields:
 * - carrier: carrier code
 * - carrierTitle: carrier title
 * - method: carrier method
 * - methodTitle: method title
 * - price: cost+handling
 * - cost: cost
 *
 * @api
 * @since 100.0.2
 */
class Method extends \Magento\Quote\Model\Quote\Address\RateResult\Method
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */


    /**
     * Round shipping carrier's method price
     *
     * @param string|float|int $price
     * @return $this
     */
    public function setPrice($price)
    {
        if (strpos($this->getMethodTitle(), "Priority Mail 1-Day") !== false) {
            $this->setMethodTitle("Priority Mail");
        } elseif (strpos($this->getMethodTitle(), "Priority Mail 2-Day") !== false) {
            $this->setMethodTitle("Priority Mail");
        } elseif (strpos($this->getMethodTitle(), "Priority Mail 3-Day") !== false) {
            $this->setMethodTitle("Priority Mail");
        }
        $this->setData('price', $this->priceCurrency->round($price));
        return $this;
    }
}
