<?php
namespace Aheadworks\Bup\Model\DataProcessor;

/**
 * Class PostDataComposite
 *
 * @package Aheadworks\Bup\Model\DataProcessor
 */
class PostDataComposite implements PostDataProcessorInterface
{
    /**
     * @var PostDataProcessorInterface[]
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
            if (!$processor instanceof PostDataProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Processor instance %s does not implement required interface.',
                        PostDataProcessorInterface::class
                    )
                );
            }
            $data = $processor->prepareEntityData($data);
        }

        return $data;
    }
}
