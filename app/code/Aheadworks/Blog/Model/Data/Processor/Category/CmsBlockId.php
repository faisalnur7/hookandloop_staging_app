<?php
namespace Aheadworks\Blog\Model\Data\Processor\Category;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;

/**
 * Class CmsBlockId
 */
class CmsBlockId implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process($data)
    {
        if (isset($data[CategoryInterface::CMS_BLOCK_ID])) {
            $data[CategoryInterface::CMS_BLOCK_ID] = $data[CategoryInterface::CMS_BLOCK_ID] ?: null;
        }

        return $data;
    }
}