<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider\Filter\Builder\Frontend;

use Aheadworks\BlogSearch\Ui\DataProvider\Filter\BuilderInterface as FilterBuilderInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\ConfigurationMismatchException;

/**
 * Class FieldValue
 */
class FieldValue implements FilterBuilderInterface
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $fieldValue;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * FieldValue constructor.
     * @param FilterBuilder $filterBuilder
     * @param string $fieldName
     * @param string $fieldValue
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        string $fieldName = '',
        string $fieldValue = ''
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->fieldName = $fieldName;
        $this->fieldValue = $fieldValue;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $filter = null;
        if (empty($this->fieldName)) {
            throw new ConfigurationMismatchException(
                __('Specify field name to add filter by')
            );
        }
        if (empty($this->fieldValue)) {
            throw new ConfigurationMismatchException(
                __('Specify field value to add filter by')
            );
        }

        $filter = $this->filterBuilder
            ->setConditionType('eq')
            ->setField($this->fieldName)
            ->setValue($this->fieldValue)
            ->create();

        return $filter;
    }
}
