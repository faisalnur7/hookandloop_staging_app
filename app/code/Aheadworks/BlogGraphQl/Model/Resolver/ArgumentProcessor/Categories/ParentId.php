<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Categories;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;

/**
 * Class ParentId
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Categories
 */
class ParentId implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        if (!isset($data['filter'][CategoryInterface::PARENT_ID])
            && !isset($data['filter'][CategoryInterface::URL_KEY])
        ) {
            $data['filter'][CategoryInterface::PARENT_ID]['eq'] = CategoryInterface::ROOT_CATEGORY_ID;
        }
        return $data;
    }
}
