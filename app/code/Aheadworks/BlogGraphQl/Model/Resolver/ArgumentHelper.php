<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ArgumentHelper
 * @package Aheadworks\BlogGraphQl\Model\Resolver
 */
class ArgumentHelper
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Return store id or default store id if not exists
     *
     * @param array $data
     * @return int
     */
    public function getStoreId($data)
    {
        return isset($data['storeId']) ? $data['storeId'] : $this->storeManager->getStore()->getId();
    }
}
