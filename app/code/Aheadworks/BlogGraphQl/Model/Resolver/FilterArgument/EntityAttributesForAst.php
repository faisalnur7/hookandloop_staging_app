<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument;

use Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst\Factory as DataPreparerFactory;
use Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst\DataPreparerInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Type;

/**
 * Class EntityAttributesForAst
 * @package Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument
 */
class EntityAttributesForAst implements FieldEntityAttributesInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var string
     */
    private $entityElementName;

    /**
     * @var DataPreparerInterface
     */
    private $dataPreparer;

    /**
     * @var array
     */
    private $additionalAttributes = [];

    /**
     * @param ConfigInterface $config
     * @param DataPreparerFactory $dataPreparerFactory
     * @param string $entityElementName
     * @param array $additionalAttributes
     */
    public function __construct(
        ConfigInterface $config,
        DataPreparerFactory $dataPreparerFactory,
        $entityElementName = null,
        array $additionalAttributes = []
    ) {
        $this->config = $config;
        $this->entityElementName = $entityElementName;
        $this->additionalAttributes = array_merge($this->additionalAttributes, $additionalAttributes);
        $this->dataPreparer = $dataPreparerFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAttributes() : array
    {
        $entityTypeSchema = $this->config->getConfigElement($this->entityElementName);
        if (!$entityTypeSchema instanceof Type) {
            throw new \LogicException(__('%1 type not defined in schema.', $this->entityElementName));
        }

        $fields = [];
        foreach ($entityTypeSchema->getFields() as $field) {
            $fields[$field->getName()] = 'String';
        }

        foreach ($this->additionalAttributes as $attribute) {
            $fields[$attribute] = 'String';
        }

        return $this->dataPreparer->getData($fields);
    }
}
