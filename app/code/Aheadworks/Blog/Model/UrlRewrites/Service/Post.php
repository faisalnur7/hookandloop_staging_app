<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Service;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\UrlRewrites\RewriteUpdater;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Post\Composite as RewriteGenerator;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\Entity\Post\Composite as RewriteCleaner;
use Aheadworks\Blog\Model\StoreSetOperations;

/**
 * Class Post
 * @package Aheadworks\Blog\Model\UrlRewrites\Service
 */
class Post
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
     * Post constructor.
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
     * @param PostInterface $post
     * @param PostInterface|null $origPost
     * @throws LocalizedException
     */
    public function updateRewrites($post, $origPost = null)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();
        $actualStores = $this->storeSetOperations->extractRealStores($post->getStoreIds());

        foreach ($actualStores as $store) {
            $storeRewrites = $this->rewriteGenerator->generate($store, $post, $origPost);
            $mergeDataProvider->merge($storeRewrites);
        }

        if ($origPost) {
            $this->rewriteCleaner->clean($post, $origPost);
        }

        $this->rewriteUpdater->update($mergeDataProvider->getData());
    }
}
