<?php
namespace Aheadworks\BlogSearch\Controller\Search;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Aheadworks\Blog\Model\Config as BlogConfig;
use Aheadworks\Blog\Model\Url as BlogUrl;
use Aheadworks\BlogSearch\Model\Url as BlogSearchUrl;
use Aheadworks\BlogSearch\Model\SearchAllowedChecker;

/**
 * Class Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var BlogConfig
     */
    private $blogConfig;

    /**
     * @var BlogUrl
     */
    private $blogUrl;

    /**
     * @var BlogSearchUrl
     */
    private $blogSearchUrl;

    /**
     * @var SearchAllowedChecker
     */
    private $searchAllowedChecker;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BlogConfig $blogConfig
     * @param BlogUrl $blogUrl
     * @param BlogSearchUrl $blogSearchUrl
     * @param SearchAllowedChecker $searchAllowedChecker
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BlogConfig $blogConfig,
        BlogUrl $blogUrl,
        BlogSearchUrl $blogSearchUrl,
        SearchAllowedChecker $searchAllowedChecker
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->blogConfig = $blogConfig;
        $this->blogUrl = $blogUrl;
        $this->blogSearchUrl = $blogSearchUrl;
        $this->searchAllowedChecker = $searchAllowedChecker;
    }

    /**
     * Search page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->searchAllowedChecker->execute()) {
            $this->messageManager->addErrorMessage(__('Search not allowed'));
            return $this->getPreparedRedirect();
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $pageConfig = $resultPage->getConfig();

        $searchQuery = $this->getRequest()->getParam(BlogSearchUrl::SEARCH_QUERY_PARAM);
        $pageConfig->getTitle()->set(__("Search Results for '%1'", $searchQuery));

        $resultPage->getLayout()->getBlock('breadcrumbs')
            ->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title'=> __('Go to Home Page'),
                    'link'=> $this->blogSearchUrl->getBaseUrl()
                ]
            )->addCrumb(
                'blog',
                [
                    'label' => $this->blogConfig->getBlogTitle(),
                    'title'=>__('Go to %1', $this->blogConfig->getBlogTitle()),
                    'link'=> $this->blogUrl->getBlogHomeUrl()
                ]
            )->addCrumb(
                'search',
                [
                    'label' => __("Search Results for '%1'", $searchQuery)
                ]
            );

        return $resultPage;
    }

    /**
     * Retrieve redirect to the current page
     *
     * @return Redirect
     */
    protected function getPreparedRedirect()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
