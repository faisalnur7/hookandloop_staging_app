<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post;

/**
 * Class ProcessorComposite
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post
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
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data)
    {
        foreach ($this->processors as $processor) {
            $data = $processor->prepareEntityData($data);
        }
        return $data;
    }
}
