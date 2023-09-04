<?php
namespace Aheadworks\Helpdesk2\Model\Rejection\Processor;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Provider
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Processor
 */
class Provider
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
     * Retrieve processor by type
     *
     * @param string $type
     * @return ProcessorInterface|null
     * @throws LocalizedException
     */
    public function getProcessor($type)
    {
        if (!array_key_exists($type, $this->processors)) {
            throw new LocalizedException(__('Unknown processor type: %1', $type));
        }
        return $this->processors[$type];
    }
}
