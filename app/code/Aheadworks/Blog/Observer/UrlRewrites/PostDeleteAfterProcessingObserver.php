<?php
namespace Aheadworks\Blog\Observer\UrlRewrites;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Delete\Post as RewriteCleaner;

/**
 * Class PostDeleteAfterProcessingObserver
 * @package Aheadworks\Blog\Observer\UrlRewrites
 */
class PostDeleteAfterProcessingObserver implements ObserverInterface
{
    /**
     * @var RewriteCleaner
     */
    private $rewriteCleaner;

    /**
     * PostDeleteAfterProcessingObserver constructor.
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
        /** @var PostInterface $post */
        $post = $observer->getEvent()->getEntity();

        if ($post) {
            $this->rewriteCleaner->clean($post);
        }

        return $this;
    }
}
