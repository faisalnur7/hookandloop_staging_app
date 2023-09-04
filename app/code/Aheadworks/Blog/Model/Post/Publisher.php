<?php
declare(strict_types=1);

namespace Aheadworks\Blog\Model\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Aheadworks\Blog\Model\ResourceModel\Post as PostResource;
use Magento\Framework\Indexer\CacheContextFactory;

/**
 * Class Publisher
 */
class Publisher
{
    /**
     * @var PostResource
     */
    private $postResource;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var CacheContextFactory
     */
    private $cacheContextFactory;

    /**
     * @param PostResource $postResource
     * @param EventManagerInterface $eventManager
     * @param CacheContextFactory $cacheContextFactory
     */
    public function __construct(
        PostResource $postResource,
        EventManagerInterface $eventManager,
        CacheContextFactory $cacheContextFactory
    ) {
        $this->postResource = $postResource;
        $this->eventManager = $eventManager;
        $this->cacheContextFactory = $cacheContextFactory;
    }

    /**
     * Publish posts
     *
     * @param PostInterface[] $posts
     * @return bool
     */
    public function publishPosts(array $posts): bool
    {
        $postIds = [];
        foreach ($posts as $post) {
            $postIds[] = $post->getId();
        }

        if ($postIds) {
            $this->postResource->updateStatus($postIds, Status::PUBLICATION);
            $this->dispatchCleanCacheByTags($posts);
            $this->dispatchMassUpdatePostsAfter($posts);
        }

        return true;
    }

    /**
     * Unpublish posts
     *
     * @param PostInterface[] $posts
     * @return bool
     */
    public function unPublishPosts(array $posts): bool
    {
        $postIds = [];
        foreach ($posts as $post) {
            $postIds[] = $post->getId();
        }

        if ($postIds) {
            $this->postResource->updateStatus($postIds, Status::DRAFT);
            $this->dispatchCleanCacheByTags($posts);
            $this->dispatchMassUpdatePostsAfter($posts);
        }

        return true;
    }

    /**
     * Dispatch clean_cache_by_tags event
     *
     * @param PostInterface[] $posts
     * @return void
     */
    private function dispatchCleanCacheByTags(array $posts = []): void
    {
        $tags = [];
        foreach ($posts as $post) {
            $tags[] = $post->getIdentities();
        }
        $tags = array_unique(array_merge(...$tags));

        if ($tags) {
            $cacheContext = $this->cacheContextFactory->create();
            $cacheContext->registerTags($tags);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $cacheContext]);
        }
    }

    /**
     * Dispatch aw_blog_post_mass_update_after event
     *
     * @param array $posts
     * @return void
     */
    private function dispatchMassUpdatePostsAfter(array $posts): void
    {
        $this->eventManager->dispatch('aw_blog_post_mass_update_after', ['entities' => $posts]);
    }
}
