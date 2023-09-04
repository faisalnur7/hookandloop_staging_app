<?php
namespace Aheadworks\Blog\Observer\UrlRewrites;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\Blog\Model\UrlRewrites\Service\Category as UrlRewritesCategoryService;
use Aheadworks\Blog\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CategorySaveAfterProcessingObserver
 * @package Aheadworks\Blog\Observer\UrlRewrites
 */
class CategorySaveAfterProcessingObserver implements ObserverInterface
{
    /**
     * @var UrlRewritesCategoryService
     */
    private $urlRewritesCategoryService;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * CategorySaveAfterProcessingObserver constructor.
     * @param CategoryFactory $categoryFactory
     * @param UrlRewritesCategoryService $urlRewritesCategoryService
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        UrlRewritesCategoryService $urlRewritesCategoryService
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->urlRewritesCategoryService = $urlRewritesCategoryService;
    }

    /**
     * Process rewrites after category saved
     *
     * @param EventObserver $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getEvent()->getEntity();

        if ($category && !empty($category->getOrigData())) {
           /** @var CategoryInterface $origCategory */
            $origCategory = $this->categoryFactory->create(['data' => $category->getOrigData()]);
        } else {
            $origCategory = null;
        }

        if ($category) {
            $this->urlRewritesCategoryService->updateRewrites($category, $origCategory);
        }

        return $this;
    }
}
