<?php
namespace Aheadworks\Blog\Observer\UrlRewrites;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\Blog\Model\UrlRewrites\Service\Post as UrlRewritesPostService;
use Aheadworks\Blog\Api\Data\PostInterfaceFactory as PostFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class PostSaveAfterProcessingObserver
 * @package Aheadworks\Blog\Observer\UrlRewrites
 */
class PostSaveAfterProcessingObserver implements ObserverInterface
{
    /**
     * @var UrlRewritesPostService
     */
    private $urlRewritesPostService;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * PostSaveAfterProcessingObserver constructor.
     * @param PostFactory $postFactory
     * @param UrlRewritesPostService $urlRewritesPostService
     */
    public function __construct(
        PostFactory $postFactory,
        UrlRewritesPostService $urlRewritesPostService
    ) {
        $this->postFactory = $postFactory;
        $this->urlRewritesPostService = $urlRewritesPostService;
    }

    /**
     * Process rewrites after post saved
     *
     * @param EventObserver $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var PostInterface $post */
        $post = $observer->getEvent()->getEntity();

        if ($post && !empty($post->getOrigData())) {
           /** @var PostInterface $origPost */
            $origPost = $this->postFactory->create(['data' => $post->getOrigData()]);
        } else {
            $origPost = null;
        }

        if ($post) {
            $this->urlRewritesPostService->updateRewrites($post, $origPost);
        }

        return $this;
    }
}
