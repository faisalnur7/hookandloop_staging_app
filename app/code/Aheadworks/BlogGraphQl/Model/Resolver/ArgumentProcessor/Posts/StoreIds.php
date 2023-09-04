<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Posts;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentHelper;

/**
 * Class StoreIds
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Posts
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
        $data['filter'][PostInterface::STORE_IDS]['eq'] = $this->argumentHelper->getStoreId($data);
        return $data;
    }
}