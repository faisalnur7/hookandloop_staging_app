<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule\CustomerAttribute;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\AttributeMetadataResolver;
use Magento\Customer\Model\Form as FormAttributes;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class AbstractAttributeMetaProvider
 */
abstract class AbstractAttributeMetaProvider
{
    /**
     * @var string[]
     */
    private $formElementMapping = [
        'input' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/input',
        'multiline' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/multiline',
        'textarea' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/textarea',
        'date' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/date',
        'checkbox' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/single-checkbox',
        'multiselect' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/multiselect',
        'select' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/select',
        'fileUploader' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/file-uploader'
    ];

    /**
     * Get form code to get attributes
     *
     * @return string
     */
    abstract protected function getFormCode(): string;

    /**
     * Get route path to update attribute data
     *
     * @return string
     */
    abstract protected function getRoutePath(): string;

    /**
     * @param FormAttributes $formAttributes
     * @param AttributeMetadataResolver $attributeMetadataResolver
     * @param EavConfig $eavConfig
     * @param ArrayManager $arrayManager
     * @param BackendUrlInterface $backendUrl
     */
    public function __construct(
        private FormAttributes $formAttributes,
        private AttributeMetadataResolver $attributeMetadataResolver,
        private EavConfig $eavConfig,
        private ArrayManager $arrayManager,
        private BackendUrlInterface $backendUrl
    ) {
    }

    /**
     * Get help desk attributes meta
     *
     * @return array
     * @throws LocalizedException
     */
    public function getHelpDeskAttributesMeta(): array
    {
        $helpDeskFormAttributes = [];
        $attributes = $this->formAttributes
            ->setFormCode($this->getFormCode())
            ->getAllowedAttributes();

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->isStatic()) {
                $helpDeskFormAttributes[] = $attribute;
            }
        }

        $meta = [];
        foreach ($helpDeskFormAttributes as $attribute) {
            $attributeMeta = $this->attributeMetadataResolver->getAttributesMeta(
                $attribute,
                $this->eavConfig->getEntityType('customer'),
                false
            );
            $meta[$attribute->getAttributeCode()] = $this->prepareAdditionalMeta($attribute, $attributeMeta);
        }

        return $meta;
    }

    /**
     * Prepare additional meta
     *
     * @param Attribute $attribute
     * @param array $attributeMeta
     * @return array
     */
    private function prepareAdditionalMeta($attribute, $attributeMeta): array
    {
        $formElement = $this->arrayManager->get('arguments/data/config/formElement', $attributeMeta);
        return $this->arrayManager->merge(
            'arguments/data/config',
            $attributeMeta,
            $this->preparePreviewMeta($formElement, $attribute->getAttributeCode())
        );
    }

    /**
     * Prepare preview data
     *
     * @param string $formElement
     * @param string $attributeCode
     * @return array
     */
    private function preparePreviewMeta($formElement, $attributeCode): array
    {
        $previewMeta = [];
        if (isset($this->formElementMapping[$formElement])) {
            $previewMeta = [
                'component' => $this->formElementMapping[$formElement],
                'requestUrl' => $this->backendUrl->getUrl(
                    $this->getRoutePath()
                ),
                'imports' => [
                    'isEditModeAllowed' => '${ $.provider }:data.is_allowed_to_update_ticket',
                ],
                'payload' => [
                    'email' => 'index = ' . $attributeCode . ':source.data.customer.email',
                    $attributeCode => 'index = ' . $attributeCode . ':source.data.customer.' . $attributeCode,
                    'ticket_id' => 'index = ' . $attributeCode . ':source.data.ticket_id',
                    'ticket_action' => 'update'
                ],
                'service' => [
                    'label' => __('Save'),
                    'buttonClasses' => 'save'
                ],
                'dataScope' => 'customer.' . $attributeCode
            ];
        }

        return $previewMeta;
    }
}
