<?php
namespace Aheadworks\Blog\Controller\Category;

use Aheadworks\Blog\Model\Source\Category\Status as CategoryStatus;
use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Block\Html\Title;
use Aheadworks\Blog\Controller\Action;

/**
 * Class View
 * @package Aheadworks\Blog\Controller\Category
 */
class View extends Action
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        if ($categoryId = (int)$this->getRequest()->getParam('blog_category_id')) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                if ($category->getStatus() == CategoryStatus::DISABLED
                    || (!in_array($this->getStoreId(), $category->getStoreIds())
                        && !in_array(0, $category->getStoreIds()))
                ) {
                    return $this->noRoute();
                }

                $resultPage = $this->resultPageFactory->create();
                $pageConfig = $resultPage->getConfig();
                $resultPage->addPageLayoutHandles(['id' => $category->getId()]);

                if ($this->areMetaTagsEnabled()) {
                    $pageConfig->getTitle()->set($this->titleResolver->getTitle($category));
                    if ($category->getMetaKeywords()) {
                        $pageConfig->setMetadata('keywords', $category->getMetaKeywords());
                    }
                    if ($category->getMetaDescription()) {
                        $pageConfig->setMetadata('description', $category->getMetaDescription());
                    }
                    if ($this->config->getCategoryCanonicalTag()) {
                        $pageConfig->addRemotePageAsset(
                            $this->url->getCategoryUrl($category),
                            'canonical',
                            ['attributes' => ['rel' => 'canonical']]
                        );
                    }
                }
                /** @var Title $pageTitleBlock */
                $pageTitleBlock = $this->_view->getLayout()->getBlock('page.main.title');
                if ($pageTitleBlock) {
                    $pageTitleBlock->setPageTitle($category->getName());
                }

                return $resultPage;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->goBack();
            }
        }

        return $this->noRoute();
    }
}
