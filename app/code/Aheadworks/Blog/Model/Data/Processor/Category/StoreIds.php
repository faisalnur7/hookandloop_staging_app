<?php
namespace Aheadworks\Blog\Model\Data\Processor\Category;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;

/**
 * Class StoreIds
 *
 * @package Aheadworks\Blog\Model\Data\Processor\Category
 */
class StoreIds implements ProcessorInterface
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
     * {@inheritdoc}
     */
    public function process($data)
    {
        if ($this->storeManager->hasSingleStore()) {
            $data[CategoryInterface::STORE_IDS] = [$this->storeManager->getStore(true)->getId()];
        }
        return $data;
    }
}
