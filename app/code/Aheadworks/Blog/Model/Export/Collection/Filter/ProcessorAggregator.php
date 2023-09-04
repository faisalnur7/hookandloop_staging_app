<?php
namespace Aheadworks\Blog\Model\Export\Collection\Filter;

use Aheadworks\Blog\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProcessorAggregator
 */
class ProcessorAggregator implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $handler;

    /**
     * @param ProcessorInterface[] $handler
     * @throws LocalizedException
     */
    public function __construct(
        $handler = []
    ) {
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function process(AbstractCollection $collection, $columnName, $value, $type = null)
    {
        if ($this->isValidHandlerType($type)) {
            $this->handler[$type]->process($collection, $columnName, $value);
        }
    }

    /**
     * Check is valid handler type
     *
     * @param null|string $type
     * @return bool
     * @throws LocalizedException
     */
    private function isValidHandlerType($type)
    {
        if (!isset($this->handler[$type])) {
            throw new LocalizedException(__(
                'No filter processor for "%type" given.',
                ['type' => $type]
            ));
        }

        if (!($this->handler[$type] instanceof ProcessorInterface)) {
            throw new LocalizedException(__(
                'Filter handler must be instance of "%interface"',
                ['interface' => ProcessorInterface::class]
            ));
        }

        return true;
    }
}