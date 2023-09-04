<?php
namespace Aheadworks\Blog\Controller\Author;

use Aheadworks\Blog\Controller\Action;

/**
 * Class ListAction
 * @package Aheadworks\Blog\Controller\Author
 */
class ListAction extends Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $pageConfig = $resultPage->getConfig();

        $pageConfig->getTitle()->set(__('Authors'));
        if ($this->areMetaTagsEnabled()) {
            $pageConfig->setMetadata('description', $this->getBlogMetaDescription());
        }

        return $resultPage;
    }
}
