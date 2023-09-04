<?php
namespace Aheadworks\Blog\Model\Service;

use Aheadworks\Blog\Model\Indexer\ProductPost\ConfigChecker;
use Aheadworks\Blog\Model\Indexer\ProductPost\IndexManagementInterface;
use Aheadworks\Blog\Model\Indexer\ProductPost\Processor as ProductPostProcessor;

class IndexService implements IndexManagementInterface
{
    /**
     * @var ProductPostProcessor
     */
    private $productPostProcessor;

    /**
     * @var ConfigChecker
     */
    private $configChecker;

    /**
     * IndexService constructor.
     * @param ProductPostProcessor $productPostProcessor
     * @param ConfigChecker $configChecker
     */
    public function __construct(
        ProductPostProcessor $productPostProcessor,
        ConfigChecker $configChecker
    ) {
        $this->productPostProcessor = $productPostProcessor;
        $this->configChecker = $configChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function processOnConfigChanges($changedPaths)
    {
        if ($this->configChecker->isChanged($changedPaths)) {
            $this->invalidateIndex();
        }
    }

    /**
     * Invalidate product post index
     *
     * @return bool
     */
    private function invalidateIndex()
    {
        try {
            $this->productPostProcessor->markIndexerAsInvalid();
            $result = true;
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }
}