<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor;

/**
 * Class Composite
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor
 */
class Composite implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof ProcessorInterface) {
                throw new \Exception('Data processor must implement ' . ProcessorInterface::class);
            }
            $data = $processor->process($data, $context);
        }
        return $data;
    }
}
