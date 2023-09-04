<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\Entity\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\Source\UrlRewrite\Metadata;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\AbstractCleaner;

/**
 * Class PostWithCategory
 * @package Aheadworks\Blog\Model\UrlRewrites\Cleaner\Entity\Post
 */
class PostWithCategory extends AbstractCleaner
{
    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * PostWithCategory constructor.
     * @param RewriteStorageInterface $rewriteStorage
     * @param array $subordinateEntitiesCleaners
     */
    public function __construct(
        RewriteStorageInterface $rewriteStorage,
        $subordinateEntitiesCleaners = []
    ) {
        parent::__construct($subordinateEntitiesCleaners);

        $this->rewriteStorage = $rewriteStorage;
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function cleanEntityRewrites($newEntityState, $oldEntityState)
    {
        $idsToDelete = [];
        $excludedCategories = array_diff($oldEntityState->getCategoryIds(), $newEntityState->getCategoryIds());

        if (!empty($excludedCategories)) {
            $allPostsRewrites = $this->rewriteStorage->findAllByData([
                UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_POST,
                UrlRewrite::ENTITY_ID => $newEntityState->getId()
            ]);

            foreach ($allPostsRewrites as $rewrite) {
                $postCategory = !empty($rewrite->getMetadata()[Metadata::REWRITE_METADATA_POST_CATEGORY])
                    ? $rewrite->getMetadata()[Metadata::REWRITE_METADATA_POST_CATEGORY]
                    : null;
                $isPostAttachedToExcludedCategory = $postCategory && in_array($postCategory, $excludedCategories);
                if ($isPostAttachedToExcludedCategory) {
                    $idsToDelete[] = $rewrite->getUrlRewriteId();
                }
            }
        }

        if (!empty($idsToDelete)) {
            $this->rewriteStorage->deleteByData([
                UrlRewrite::URL_REWRITE_ID => $idsToDelete
            ]);
        }
    }
}
