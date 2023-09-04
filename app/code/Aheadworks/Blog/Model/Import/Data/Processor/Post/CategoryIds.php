<?php
namespace Aheadworks\Blog\Model\Import\Data\Processor\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;

/**
 * Class CategoryIds
 */
class CategoryIds implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process($data)
    {
        if (isset($data[PostInterface::CATEGORY_IDS]) && !empty($data[PostInterface::CATEGORY_IDS])) {
            $data[PostInterface::CATEGORY_IDS] = explode(',', (string)$data[PostInterface::CATEGORY_IDS]);
        }

        return $data;
    }
}
