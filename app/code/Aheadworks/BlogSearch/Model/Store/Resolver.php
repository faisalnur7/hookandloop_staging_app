<?php
namespace Aheadworks\BlogSearch\Model\Store;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Resolver
 */
class Resolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Resolver constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve current store
     *
     * @return StoreInterface|null
     */
    public function getCurrentStore()
    {
        try {
            $currentStore = $this->storeManager->getStore();
        } catch (LocalizedException $exception) {
            $currentStore = null;
        }
        return $currentStore;
    }
}
