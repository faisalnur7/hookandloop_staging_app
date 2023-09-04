<?php
namespace Aheadworks\Blog\Model\Import\Data\Processor\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;

/**
 * Class TagNames
 */
class TagNames implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process($data)
    {
        if (isset($data[PostInterface::TAG_NAMES]) && !empty($data[PostInterface::TAG_NAMES])) {
            $data[PostInterface::TAG_NAMES] = explode(',', (string)$data[PostInterface::TAG_NAMES]);
        }

        return $data;
    }
}
