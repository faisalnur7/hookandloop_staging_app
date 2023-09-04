<?php
namespace Aheadworks\Blog\Controller;

use Aheadworks\Blog\Api\AuthorRepositoryInterface;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Api\TagRepositoryInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Url;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Blog\Model\Url\TypeResolver as UrlTypeResolver;
use Aheadworks\Blog\Controller\Checker as DataChecker;
use Aheadworks\Blog\Model\Resolver\Title as TitleResolver;
use \Aheadworks\Blog\Model\Seo\CanonicalIncluder;

/**
 * Class Action
 * @package Aheadworks\Blog\Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Action extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @var AuthorRepositoryInterface
     */
    protected $authorRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var UrlTypeResolver
     */
    protected $urlTypeResolver;

    /**
     * @var DataChecker
     */
    protected $dataChecker;

    /**
     * @var TitleResolver
     */
    protected $titleResolver;

    /**
     * @var CanonicalIncluder
     */
    protected $canonicalIncluder;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param StoreManagerInterface $storeManager
     * @param Registry $coreRegistry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PostRepositoryInterface $postRepository
     * @param TagRepositoryInterface $tagRepository
     * @param AuthorRepositoryInterface $authorRepository
     * @param Config $config
     * @param Url $url
     * @param UrlTypeResolver $urlTypeResolver
     * @param Checker $dataChecker
     * @param TitleResolver $titleResolver
     * @param CanonicalIncluder $canonicalIncluder
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        Registry $coreRegistry,
        CategoryRepositoryInterface $categoryRepository,
        PostRepositoryInterface $postRepository,
        TagRepositoryInterface $tagRepository,
        AuthorRepositoryInterface $authorRepository,
        Config $config,
        Url $url,
        UrlTypeResolver $urlTypeResolver,
        DataChecker $dataChecker,
        TitleResolver $titleResolver,
        CanonicalIncluder $canonicalIncluder
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        $this->authorRepository = $authorRepository;
        $this->config = $config;
        $this->url = $url;
        $this->urlTypeResolver = $urlTypeResolver;
        $this->dataChecker = $dataChecker;
        $this->titleResolver = $titleResolver;
        $this->canonicalIncluder = $canonicalIncluder;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->config->isBlogEnabled()) {
            /**  @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->forward('noroute');
        }
        $this->coreRegistry->register('blog_action', true, true);
        return parent::dispatch($request);
    }

    /**
     * Retrieves blog title
     *
     * @return string
     */
    protected function getBlogTitle()
    {
        return $this->config->getBlogTitle();
    }

    /**
     * Retrieves blog meta description
     *
     * @return mixed
     */
    protected function getBlogMetaDescription()
    {
        return $this->config->getBlogMetaDescription();
    }

    /**
     * Retrieves blog meta keywords
     *
     * @return mixed
     */
    protected function getBlogMetaKeywords()
    {
        return $this->config->getBlogMetaKeywords();
    }

    /**
     * Check are meta tags enabled
     *
     * @return bool
     */
    protected function areMetaTagsEnabled()
    {
        return $this->config->areMetaTagsEnabled();
    }

    /**
     * Get current store ID
     *
     * @return int
     */
    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Go back
     *
     * @return Redirect
     */
    protected function goBack()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     * No route forward
     *
     * @return Forward
     */
    protected function noRoute()
    {
        /**  @var Forward $forward */
        $forward = $this->resultForwardFactory->create();

        return $forward
            ->setModule('cms')
            ->setController('noroute')
            ->forward('index');
    }
}
