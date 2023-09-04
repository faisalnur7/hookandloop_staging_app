<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\CustomerAttribute;

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\FileUploaderDataResolver;
use Magento\Ui\Component\Form\Element\Multiline;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ComponentDataResolver
 */
class ComponentDataResolver
{
    /**
     * ComponentDataResolver constructor.
     *
     * @param FileUploaderDataResolver $fileUploaderDataResolver
     */
    public function __construct(private FileUploaderDataResolver $fileUploaderDataResolver)
    {
    }

    /**
     * Override file uploader UI component data
     *
     * Overrides data for attributes with frontend_input equal to 'image' or 'file'.
     *
     * @param Customer|Address $entity
     * @param array $entityData
     * @return void
     * @throws LocalizedException
     */
    public function overrideCustomerAttributesData($entity, array &$entityData): void
    {
        $this->fileUploaderDataResolver->overrideFileUploaderData($entity, $entityData);
        $this->overrideMultilineData($entity, $entityData);
    }

    /**
     * Override multiline data
     *
     * @param Customer|Address $entity
     * @param array $entityData
     * @return void
     * @throws LocalizedException
     */
    private function overrideMultilineData($entity, array &$entityData): void
    {
        $attributes = $entity->getAttributes();
        foreach ($attributes as $attribute) {
            /** @var Attribute $attribute */
            if ($attribute->getFrontendInput() === Multiline::NAME) {
                $attributeCode = $attribute->getAttributeCode();
                $data = $entityData[$attributeCode] ?? '';
                $entityData[$attributeCode] =  explode("\n", $data);
            }
        }
    }
}
