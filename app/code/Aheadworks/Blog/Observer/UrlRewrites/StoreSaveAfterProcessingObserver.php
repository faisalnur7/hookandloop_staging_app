<?php
namespace Aheadworks\Blog\Observer\UrlRewrites;

use Aheadworks\Blog\Model\UrlRewrites\Service\Config as UrlRewriteConfigService;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

/**
 * Class StoreSaveAfterProcessingObserver
 */
class StoreSaveAfterProcessingObserver implements ObserverInterface
{
    /**
     * @var UrlRewriteConfigService
     */
    private $urlRewriteConfigService;

    /**
     * StoreSaveAfterProcessingObserver constructor.
     * @param UrlRewriteConfigService $urlRewriteConfigService
     */
    public function __construct(
        UrlRewriteConfigService $urlRewriteConfigService
    ) {
        $this->urlRewriteConfigService = $urlRewriteConfigService;
    }

    /**
     * Process rewrites after store saved
     *
     * @param EventObserver $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var Store $store */
        $store = $observer->getEvent()->getStore();
        $isNewStore = !$store->getStoredData();

        if ($isNewStore && $storeId = $store->getStoreId()) {
            $this->urlRewriteConfigService->generateAllRewrites([$storeId]);
        }

        return $this;
    }
}
