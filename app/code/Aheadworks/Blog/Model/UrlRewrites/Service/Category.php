<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Service;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\UrlRewrites\RewriteUpdater;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Category\Category as RewriteGenerator;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\Entity\Category\Category as RewriteCleaner;
use Aheadworks\Blog\Model\StoreSetOperations;

/**
 * Class Category
 * @package Aheadworks\Blog\Model\UrlRewrites\Service
 */
class Category
{
    /**
     * @var RewriteUpdater
     */
    private $rewriteUpdater;

    /**
     * @var RewriteGenerator
     */
    private $rewriteGenerator;

    /**
     * @var MergeDataProviderFactory
     */
    private $mergeDataProviderFactory;

    /**
     * @var RewriteCleaner
     */
    private $rewriteCleaner;

    /**
     * @var StoreSetOperations
     */
    private $storeSetOperations;

    /**
     * Category constructor.
     * @param RewriteUpdater $rewriteUpdater
     * @param RewriteGenerator $rewriteGenerator
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param RewriteCleaner $rewriteCleaner
     * @param StoreSetOperations $storeSetOperations
     */
    public function __construct(
        RewriteUpdater $rewriteUpdater,
        RewriteGenerator $rewriteGenerator,
        MergeDataProviderFactory $mergeDataProviderFactory,
        RewriteCleaner $rewriteCleaner,
        StoreSetOperations $storeSetOperations
    ) {
        $this->rewriteUpdater = $rewriteUpdater;
        $this->rewriteGenerator = $rewriteGenerator;
        $this->mergeDataProviderFactory = $mergeDataProviderFactory;
        $this->rewriteCleaner = $rewriteCleaner;
        $this->storeSetOperations = $storeSetOperations;
    }

    /**
     * Update rewrites for category
     *
     * @param CategoryInterface $category
     * @param CategoryInterface|null $origCategory
     * @throws LocalizedException
     */
    public function updateRewrites($category, $origCategory = null)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();
        $actualStores = $this->storeSetOperations->extractRealStores($category->getStoreIds());

        foreach ($actualStores as $store) {
            $storeRewrites = $this->rewriteGenerator->generate($store, $category, $origCategory);
            $mergeDataProvider->merge($storeRewrites);
        }

        if ($origCategory) {
            $this->rewriteCleaner->clean($category, $origCategory);
        }

        $this->rewriteUpdater->update($mergeDataProvider->getData());
    }
}
