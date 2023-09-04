<?php
namespace Aheadworks\BlogSearch\ViewModel;

use Aheadworks\BlogSearch\Model\Url as BlogSearchUrl;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\BlogSearch\Model\Config as BlogSearchConfig;

/**
 * Class SearchField
 */
class SearchField implements ArgumentInterface
{
    /**
     * @var BlogSearchUrl
     */
    private $blogSearchUrl;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var BlogSearchConfig
     */
    private $blogSearchConfig;

    /**
     * SearchField constructor.
     * @param BlogSearchUrl $blogSearchUrl
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param BlogSearchConfig $blogSearchConfig
     */
    public function __construct(
        BlogSearchUrl $blogSearchUrl,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        BlogSearchConfig $blogSearchConfig
    ) {
        $this->blogSearchUrl = $blogSearchUrl;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->blogSearchConfig = $blogSearchConfig;
    }

    /**
     * Returns blog search url
     *
     * @return string
     */
    public function getBlogSearchUrl()
    {
        $currentStore = $this->storeManager->getStore()->getId();

        return $this->blogSearchUrl->getBlogSearchUrl($currentStore);
    }

    /**
     * Returns search query parameter name
     *
     * @return string
     */
    public function getSearchQueryParamName()
    {
        return BlogSearchUrl::SEARCH_QUERY_PARAM;
    }

    /**
     * Returns search query value
     *
     * @return mixed
     */
    public function getSearchQueryValue()
    {
        return $this->request->getParam(BlogSearchUrl::SEARCH_QUERY_PARAM);
    }

    /**
     * Returns search query min length
     *
     * @return int
     */
    public function getSearchQueryMinLength()
    {
        return (int)$this->blogSearchConfig->getSearchQueryMinLength();
    }

    /**
     * Returns search query max length
     *
     * @return int
     */
    public function getSearchQueryMaxLength()
    {
        return (int)$this->blogSearchConfig->getSearchQueryMaxLength();
    }
}
