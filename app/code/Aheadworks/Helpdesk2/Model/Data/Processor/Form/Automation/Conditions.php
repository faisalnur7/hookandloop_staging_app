<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Automation;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Automation\Condition as ConditionSource;
use Aheadworks\Helpdesk2\Model\Source\Automation\Operator as OperatorSource;
use Aheadworks\Helpdesk2\Model\Automation\ValueMapper;

/**
 * Class Conditions
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Automation
 */
class Conditions implements ProcessorInterface
{
    /**
     * @var ConditionSource
     */
    private $conditionSource;

    /**
     * @var OperatorSource
     */
    private $operatorSource;

    /**
     * @var ValueMapper
     */
    private $valueMapper;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ConditionSource $conditionSource
     * @param OperatorSource $operatorSource
     * @param ValueMapper $valueMapper
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ConditionSource $conditionSource,
        OperatorSource $operatorSource,
        ValueMapper $valueMapper,
        ArrayManager $arrayManager
    ) {
        $this->conditionSource = $conditionSource;
        $this->operatorSource = $operatorSource;
        $this->valueMapper = $valueMapper;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareMetaData($meta)
    {
        $meta = $this->arrayManager->set(
            'aw_helpdesk2_automation_form_data_source/arguments/data/js_config/value_mapper/conditions',
            $meta,
            [
                'object_options' => $this->conditionSource->getAvailableOptionsByEventType(),
                'operator_options' => $this->operatorSource->getAvailableOptionsByConditionType(),
                'value_options' => $this->valueMapper->getAvailableConditionValuesByConditionType()
            ]
        );

        return $meta;
    }
}
