<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form;

/**
 * Class ProcessorComposite
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form
 */
class ProcessorComposite implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        foreach ($this->processors as $processor) {
            $data = $processor->prepareEntityData($data);
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($data)
    {
        foreach ($this->processors as $processor) {
            $data = $processor->prepareMetaData($data);
        }
        return $data;
    }
}
