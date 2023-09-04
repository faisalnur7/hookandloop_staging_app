<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class CategoryIds
 * @package Aheadworks\Blog\Model\Data\Processor\Post
 */
class CategoryIds implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (!isset($data[PostInterface::CATEGORY_IDS]) || empty($data[PostInterface::CATEGORY_IDS])) {
            $data[PostInterface::CATEGORY_IDS] = [];
        }

        return $data;
    }
}
