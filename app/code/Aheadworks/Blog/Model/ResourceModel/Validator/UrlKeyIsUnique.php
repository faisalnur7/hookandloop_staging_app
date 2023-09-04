<?php
namespace Aheadworks\Blog\Model\ResourceModel\Validator;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\TypeResolver;
use Aheadworks\Blog\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Aheadworks\Blog\Model\ResourceModel\Author as ResourceAuthor;
use Aheadworks\Blog\Model\Resolver\UrlKeyUniqueVerifiableStores as UrlKeyUniqueVerifiableStoresResolver;

/**
 * Class UrlKeyIsUnique
 */
class UrlKeyIsUnique
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var UrlKeyUniqueVerifiableStoresResolver
     */
    private $urlKeyUniqueVerifiableStoresResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param TypeResolver $typeResolver
     * @param ResourceConnection $resourceConnection
     * @param UrlKeyUniqueVerifiableStoresResolver $urlKeyUniqueVerifiableStoresResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        TypeResolver $typeResolver,
        ResourceConnection $resourceConnection,
        UrlKeyUniqueVerifiableStoresResolver $urlKeyUniqueVerifiableStoresResolver
    ) {
        $this->metadataPool = $metadataPool;
        $this->typeResolver = $typeResolver;
        $this->resourceConnection = $resourceConnection;
        $this->urlKeyUniqueVerifiableStoresResolver = $urlKeyUniqueVerifiableStoresResolver;
    }

    /**
     * Checks whether the URL-Key is unique
     *
     * @param object $entity
     * @return bool
     * @throws \Exception
     */
    public function validate($entity)
    {
        $entityType = $this->typeResolver->resolve($entity);
        $metaData = $this->metadataPool->getMetadata($entityType);
        $connection = $this->resourceConnection
            ->getConnectionByName($metaData->getEntityConnectionName());

        $checkTables = [
            $this->resourceConnection->getTableName(ResourcePost::BLOG_POST_TABLE),
            $this->resourceConnection->getTableName(ResourceCategory::BLOG_CATEGORY_TABLE),
            $this->resourceConnection->getTableName(ResourceAuthor::BLOG_AUTHOR_TABLE)
        ];
        foreach ($checkTables as $table) {
            $select = $connection->select()
                ->from($table)
                ->where('url_key = :url_key');
            $bind = ['url_key' => $entity->getUrlKey()];

            if ($table === ResourcePost::BLOG_POST_TABLE) {
                $select->joinInner(
                    ['post_store_table' => $this->resourceConnection->getTableName(ResourcePost::BLOG_POST_STORE_TABLE)],
                    'post_store_table.post_id =' . $table . '.' . PostInterface::ID,
                    []
                )->where('post_store_table.store_id IN (?)', $this->resolveUrlKeyUniqueVerifiableStoreIds($entity));
            }

            if ($table === ResourceCategory::BLOG_CATEGORY_TABLE) {
                $select->joinInner(
                    ['category_store_table' => $this->resourceConnection->getTableName(ResourceCategory::BLOG_CATEGORY_STORE_TABLE)],
                    'category_store_table.category_id =' . $table . '.' . CategoryInterface::ID,
                    []
                )->where('category_store_table.store_id IN (?)', $this->resolveUrlKeyUniqueVerifiableStoreIds($entity));
            }

            if ($entity->getId() && $table == $metaData->getEntityTable()) {
                $select->where($metaData->getIdentifierField() . ' <> :id');
                $bind['id'] = $entity->getId();
            }
            if ($connection->fetchRow($select, $bind)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Resolves store ids that must be checked for url key unique
     *
     * @param $entity
     * @return int[]
     */
    private function resolveUrlKeyUniqueVerifiableStoreIds($entity)
    {
        try {
            $storeIds = $entity->getStoreIds() ?: [];
        } catch (\Error $e) {
            $storeIds = [];
        }

        return $this->urlKeyUniqueVerifiableStoresResolver->getStoresToVerify($storeIds);
    }
}
