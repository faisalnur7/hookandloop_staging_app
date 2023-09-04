<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator;

use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Aheadworks\Blog\Model\Config;

/**
 * Class AbstractGenerator
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator
 */
abstract class AbstractGenerator
{
    /**
     * @var MergeDataProviderFactory
     */
    protected $mergeDataProviderFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AbstractGenerator[]
     */
    private $subordinateEntitiesGenerators;

    /**
     * AbstractGenerator constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        $this->mergeDataProviderFactory = $mergeDataProviderFactory;
        $this->config = $config;
        $this->subordinateEntitiesGenerators = $subordinateEntitiesGenerators;
    }

    /**
     * Generates url rewrites
     *
     * @param int $storeId
     * @param mixed $newEntityState
     * @param mixed|null $oldEntityState
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    public function generate($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        $isNewEntity = $oldEntityState == null;

        if (!$isNewEntity
            && $this->config->getSaveRewritesHistory($storeId)
            && $this->isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)) {
            $mergeDataProvider->merge($this->getPermanentRedirects($storeId, $newEntityState, $oldEntityState));
        }
        if ($this->isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)) {
            $mergeDataProvider->merge($this->getControllerRewrites($storeId, $newEntityState));
        }
        if (!$isNewEntity && !empty($mergeDataProvider->getData())) {
            $mergeDataProvider->merge($this->getExistingRewrites($storeId, $newEntityState, $oldEntityState));
        }

        $mergeDataProvider->merge($this->getSubordinateEntitiesRewrites($storeId, $newEntityState, $oldEntityState));

        return $mergeDataProvider->getData();
    }

    /**
     * Checks if need generate permanent redirects
     *
     * @param int $storeId
     * @param mixed $newEntityState
     * @param mixed $oldEntityState
     * @return bool
     */
    abstract protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState);

    /**
     * Checks if need generate controller rewrites
     *
     * @param int $storeId
     * @param mixed $newEntityState
     * @param mixed|null $oldEntityState
     * @return bool
     */
    abstract protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState);

    /**
     * Returns permanent redirect rewrites
     *
     * @param int $storeId
     * @param mixed $newEntityState
     * @param mixed $oldEntityState
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    abstract protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState);

    /**
     * Returns controller rewrites
     *
     * @param int $storeId
     * @param mixed $newEntityState
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    abstract protected function getControllerRewrites($storeId, $newEntityState);

    /**
     * Returns existing rewrites
     *
     * @param int $storeId
     * @param mixed $newEntityState
     * @param mixed $oldEntityState
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    abstract protected function getExistingRewrites($storeId, $newEntityState, $oldEntityState);

    /**
     * Returns rewrites for subordinate entities(for example article subordinate for category)
     *
     * @param int $storeId
     * @param mixed $newEntityState
     * @param mixed|null $oldEntityState
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    private function getSubordinateEntitiesRewrites($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        /** @var AbstractGenerator $generator */
        foreach ($this->subordinateEntitiesGenerators as $generator) {
            $mergeDataProvider->merge($generator->generate($storeId, $newEntityState ,$oldEntityState));
        }

        return  $mergeDataProvider->getData();
    }
}
