<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Categories;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Source\Category\Status as CategoryStatus;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;

/**
 * Class Status
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Categories
 */
class Status implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        $data['filter'][CategoryInterface::STATUS]['eq'] = CategoryStatus::ENABLED;
        return $data;
    }
}
