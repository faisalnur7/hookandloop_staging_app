<?php

namespace Ravedigital\Custom\Plugin\Carrier;

use Magento\Quote\Api\Data\ShippingMethodInterfaceFactory;


class DeliveryDate
{
    /**
     * @var ShippingMethodInterfaceFactory
     */
    protected $extensionFactory;

    /**
     * DeliveryDate constructor.
     * @param ShippingMethodInterfaceFactory $extensionFactory
     */
    public function __construct(
        ShippingMethodInterfaceFactory $extensionFactory
    )
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param $subject
     * @param $result
     * @param $rateModel
     * @return mixed
     */
    public function afterModelToDataObject($subject, $result, $rateModel)
    {
        $extensionAttribute = $result->getExtensionAttributes() ?
            $result->getExtensionAttributes()
            :
            $this->extensionFactory->create()
        ;
        $extensionAttribute->setEtaDeliveryDate($rateModel->getEtaDeliveryDate());
        $extensionAttribute->setShippingTitle($rateModel->getShippingTitle());

        $result->setExtensionAttributes($extensionAttribute);
        return $result;
    }
}