<?php
namespace Ravedigital\Custom\Plugin\Quote\Address;

class Rate
{
    /**
     * @param \Magento\Quote\Model\Quote\Address\AbstractResult $rate
     * @return \Magento\Quote\Model\Quote\Address\Rate
     */
    public function afterImportShippingRate($subject, $result, $rate)
    {
        if ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            $result->setEtaDeliveryDate(
                $rate->getEtaDeliveryDate()
            );
        }
        if ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            $result->setShippingTitle(
                $rate->getShippingTitle()
            );
        }

        return $result;
    }
}
