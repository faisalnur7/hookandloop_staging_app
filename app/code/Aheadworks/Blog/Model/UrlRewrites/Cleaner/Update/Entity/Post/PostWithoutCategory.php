<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\Entity\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\StoreSetOperations;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\AbstractCleaner;

/**
 * Class PostWithoutCategory
 * @package Aheadworks\Blog\Model\UrlRewrites\Cleaner\Entity\Post
 */
class PostWithoutCategory extends AbstractCleaner
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
     * PostWithoutCategory constructor.
     * @param RewriteStorageInterface $rewriteStorage
     * @param StoreSetOperations $storeSetOperations
     * @param array $subordinateEntitiesCleaners
     */
    public function __construct(
        RewriteStorageInterface $rewriteStorage,
        StoreSetOperations $storeSetOperations,
        $subordinateEntitiesCleaners = []
    ) {
        parent::__construct($subordinateEntitiesCleaners);

        $this->rewriteStorage = $rewriteStorage;
        $this->storeSetOperations = $storeSetOperations;
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function cleanEntityRewrites($newEntityState, $oldEntityState)
    {
        $excludedStores = $this->storeSetOperations->getDifference($oldEntityState->getStoreIds(), $newEntityState->getStoreIds());

        if (!empty($excludedStores)) {
            $this->rewriteStorage->deleteByData([
                UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_POST,
                UrlRewrite::ENTITY_ID => $newEntityState->getId(),
                UrlRewrite::STORE_ID => $excludedStores
            ]);
        }

    }
}
