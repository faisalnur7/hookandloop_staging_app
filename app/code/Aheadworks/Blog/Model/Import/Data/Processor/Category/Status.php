<?php
namespace Aheadworks\Blog\Model\Import\Data\Processor\Category;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Model\Source\Category\Status as SourceStatus;

/**
 * Class Status
 */
class Status implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process($data)
    {
        $data[CategoryInterface::STATUS] = (int)($data[CategoryInterface::STATUS] ?? SourceStatus::DISABLED);

        return $data;
    }
}