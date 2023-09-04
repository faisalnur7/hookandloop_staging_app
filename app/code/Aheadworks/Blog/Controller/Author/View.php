<?php
namespace Aheadworks\Blog\Controller\Author;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Controller\Action;

/**
 * Class View
 * @package Aheadworks\Blog\Controller\Author
 */
class View extends Action
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($authorId = (int)$this->getRequest()->getParam('author_id')) {
            try {
                $author = $this->authorRepository->get($authorId);
                $resultPage = $this->resultPageFactory->create();
                $pageConfig = $resultPage->getConfig();
                $resultPage->addPageLayoutHandles(['id' => $authorId]);
                if ($this->areMetaTagsEnabled()) {
                    $pageConfig->getTitle()->set($this->titleResolver->getTitle($author));
                    if ($author->getMetaKeywords()) {
                        $pageConfig->setMetadata('keywords', $author->getMetaKeywords());
                    }
                    if ($author->getMetaDescription()) {
                        $pageConfig->setMetadata('description', $author->getMetaDescription());
                    }
                    if ($this->config->getAuthorCanonicalTag()) {
                        $pageConfig->addRemotePageAsset(
                            $this->url->getAuthorUrl($author),
                            'canonical',
                            ['attributes' => ['rel' => 'canonical']]
                        );
                    }
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
