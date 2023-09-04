<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Categories;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentHelper;

/**
 * Class StoreIds
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Categories
 */
class StoreIds implements ProcessorInterface
{
    /**
     * @var ArgumentHelper
     */
    private $argumentHelper;

    /**
     * @param ArgumentHelper $argumentHelper
     */
    public function __construct(
        ArgumentHelper $argumentHelper
    ) {
        $this->argumentHelper = $argumentHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        $data['filter'][CategoryInterface::STORE_IDS]['eq'] = $this->argumentHelper->getStoreId($data);
        return $data;
    }
}