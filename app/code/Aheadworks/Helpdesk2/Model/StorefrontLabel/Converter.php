<?php
namespace Aheadworks\Helpdesk2\Model\StorefrontLabel;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterfaceFactory;

/**
 * Class Converter
 *
 * @package Aheadworks\Helpdesk2\Model\StorefrontLabel
 */
class Converter
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var StorefrontLabelInterfaceFactory
     */
    private $storefrontLabelFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param StorefrontLabelInterfaceFactory $storefrontLabelFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        StorefrontLabelInterfaceFactory $storefrontLabelFactory

    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storefrontLabelFactory = $storefrontLabelFactory;
    }

    /**
     * Convert to data object
     *
     * @param StorefrontLabelInterface|array $label
     * @return StorefrontLabelInterface
     */
    public function convertToDataObject($label)
    {
        if ($label instanceof StorefrontLabelInterface) {
            $labelObject = $label;
        } else {
            /** @var StorefrontLabelInterface $labelObject */
            $labelObject = $this->storefrontLabelFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $labelObject,
                $label,
                StorefrontLabelInterface::class
            );
        }

        return $labelObject;
    }
}
