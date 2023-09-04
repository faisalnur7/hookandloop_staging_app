<?php
namespace Aheadworks\Blog\Observer;

use Aheadworks\Blog\Model\Service\IndexService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ConfigBlogSection
 */
class ConfigBlogSection implements ObserverInterface
{
    /**
     * @var IndexService
     */
    private $indexService;

    /**
     * ConfigBlogSection constructor.
     * @param IndexService $indexService
     */
    public function __construct(
        IndexService $indexService
    ) {
        $this->indexService = $indexService;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $changedPaths = $observer->getEvent()->getChangedPaths();
        $this->indexService->processOnConfigChanges($changedPaths);
    }
}