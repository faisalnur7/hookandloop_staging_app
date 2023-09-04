<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\Entity\Category;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\StoreSetOperations;
use Aheadworks\Blog\Model\UrlRewrites\Finder\Post as PostRewritesFinder;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\AbstractCleaner;

/**
 * Class Posts
 * @package Aheadworks\Blog\Model\UrlRewrites\Cleaner\Entity\Category
 */
class Posts extends AbstractCleaner
{
    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * @var StoreSetOperations
     */
    private $storeSetOperations;

    /**
     * @var PostRewritesFinder
     */
    private $postRewritesFinder;

    /**
     * Posts constructor.
     * @param RewriteStorageInterface $rewriteStorage
     * @param StoreSetOperations $storeSetOperations
     * @param PostRewritesFinder $postRewritesFinder
     * @param array $subordinateEntitiesCleaners
     */
    public function __construct(
        RewriteStorageInterface $rewriteStorage,
        StoreSetOperations $storeSetOperations,
        PostRewritesFinder $postRewritesFinder,
        $subordinateEntitiesCleaners = []
    ) {
        parent::__construct($subordinateEntitiesCleaners);

        $this->rewriteStorage = $rewriteStorage;
        $this->storeSetOperations = $storeSetOperations;
        $this->postRewritesFinder = $postRewritesFinder;
    }

    /**
     * @inheritdoc
     * @param CategoryInterface $newEntityState
     * @param CategoryInterface $oldEntityState
     */
    protected function cleanEntityRewrites($newEntityState, $oldEntityState)
    {
        $postIdsToDelete = [];
        $excludedStores = $this->storeSetOperations->getDifference($oldEntityState->getStoreIds(), $newEntityState->getStoreIds());

        if ($excludedStores) {
            foreach ($this->postRewritesFinder->getCategoryPostsRewrites($newEntityState, $excludedStores) as $postRewrite) {
                $postIdsToDelete[] = $postRewrite->getUrlRewriteId();
            }
        }

        if (!empty($postIdsToDelete)) {
            $this->rewriteStorage->deleteByData([
                UrlRewrite::URL_REWRITE_ID => $postIdsToDelete
            ]);
        }
    }
}
