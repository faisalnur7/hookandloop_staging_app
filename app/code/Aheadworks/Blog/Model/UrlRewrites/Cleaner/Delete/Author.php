<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Cleaner\Delete;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class Author
 * @package Aheadworks\Blog\Model\UrlRewrites\Cleaner\Delete\Entity\Category
 */
class Author extends AbstractCleaner
{
    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * Author constructor.
     * @param RewriteStorageInterface $rewriteStorage
     * @param array $subordinateEntitiesCleaners
     */
    public function __construct(
        RewriteStorageInterface $rewriteStorage,
        $subordinateEntitiesCleaners = []
    ) {
        $this->rewriteStorage = $rewriteStorage;

        parent::__construct($subordinateEntitiesCleaners);
    }

    /**
     * @inheritdoc
     * @param AuthorInterface $deletedEntity
     */
    protected function cleanEntityRewrites($deletedEntity)
    {
        $this->rewriteStorage->deleteByData([
            UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_AUTHOR,
            UrlRewrite::ENTITY_ID => $deletedEntity->getId()
        ]);
    }
}
