<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Tags;

use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentHelper;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;
use Magento\Store\Model\Store;

/**
 * Class PostsStore
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Tags
 */
class PostsStore implements ProcessorInterface
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
        $data['filter']['posts_store']['in'] = [$this->argumentHelper->getStoreId($data), Store::DEFAULT_STORE_ID];
        return $data;
    }
}
