<?php
namespace Aheadworks\Blog\Observer\UrlRewrites;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Delete\Category\Category as RewriteCleaner;

/**
 * Class CategoryDeleteAfterProcessingObserver
 * @package Aheadworks\Blog\Observer\UrlRewrites
 */
class CategoryDeleteAfterProcessingObserver implements ObserverInterface
{
    /**
     * @var RewriteCleaner
     */
    private $rewriteCleaner;

    /**
     * CategoryDeleteAfterProcessingObserver constructor.
     * @param RewriteCleaner $rewriteCleaner
     */
    public function __construct(
        RewriteCleaner $rewriteCleaner
    ) {
        $this->rewriteCleaner = $rewriteCleaner;
    }

    /**
     * Process rewrites deleting after entity deleted
     *
     * @param EventObserver $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getEvent()->getEntity();

        if ($category) {
            $this->rewriteCleaner->clean($category);
        }

        return $this;
    }
}
