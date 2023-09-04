<?php
/**
 * @package    Blog
 * @version    1.0.0

 */
namespace Ravedigital\Blog\Block;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Blog\Model\Post\FeaturedImageInfo;
use Aheadworks\Blog\Model\Source\Config\Seo\UrlType;
use Aheadworks\Blog\Post\ListingFactory;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Source\Category\Status as CategoryStatus;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\TagRepositoryInterface;
use Aheadworks\Blog\Block\Html\Pager;
use Aheadworks\Blog\Block\Post as PostBlock;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Url;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Blog\Model\Category\Breadcrumb\DataProvider;

/**
 * List of posts block
 * @package Aheadworks\Blog\Block
 */
class PostList extends \Magento\Framework\View\Element\Template implements IdentityInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DataProvider
     */
    protected $dataProvider;

     /**
      * @var FeaturedImageInfo
      */
    protected $imageInfo;

     /**
      * @var Post\Listing
      */
    protected $postListing;
     
     /**
      * @var CategoryRepositoryInterface
      */
    protected $categoryRepository;

    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @param Context $context
     * @param DataProvider $dataProvider
     * @param Post\ListingFactory $postListingFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param TagRepositoryInterface $tagRepository
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        FeaturedImageInfo $imageInfo,
        Post\ListingFactory $postListingFactory,
        DataProvider $dataProvider,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LinkFactory $linkFactory,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository,
        Url $url,
        Config $config,
        array $data = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->linkFactory = $linkFactory;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->url = $url;
        $this->config = $config;
        $this->postListing = $postListingFactory->create();
        $this->dataProvider = $dataProvider;
        $this->imageInfo = $imageInfo;
        parent::__construct($context, $data);
    }

    /**
     * @return PostInterface[]
     */
    public function getPosts()
    {
        return $this->postListing->getPosts();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->applyPagination();

        /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $tagId = (int)$this->getRequest()->getParam('tag_id');
            $categoryId = (int)$this->getRequest()->getParam('blog_category_id');

            $blogTitle = $this->config->getBlogTitle();
            if (!$tagId && !$categoryId) {
                $breadcrumbs->addCrumb('blog_home', ['label' => $blogTitle]);
            } else {
                $breadcrumbs->addCrumb(
                    'blog_home',
                    [
                        'label' => $blogTitle,
                        'link' => $this->url->getBlogHomeUrl(),
                    ]
                );
                if ($tagId) {
                    $tag = $this->tagRepository->get($tagId);
                    $breadcrumbs->addCrumb(
                        'search_by_tag',
                        ['label' => __("Tagged with '%1'", $tag->getName())]
                    );
                }
                if ($categoryId) {
                    $category = $this->categoryRepository->get($categoryId);
                    $breadcrumbPath = $this->dataProvider->getBreadcrumbPath($category);
                    foreach ($breadcrumbPath as $key => $crumbInfo) {
                        $breadcrumbs->addCrumb('category_view_' . $key, $crumbInfo);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Retrieves items list html
     *
     * @param PostInterface $post
     * @return string
     */
    public function getItemHtml(PostInterface $post)
    {
        /** @var PostBlock $block */
        $block = $this->getLayout()->createBlock(
            PostBlock::class,
            '',
            [
                'data' => [
                    'post' => $post,
                    'mode' => PostBlock::MODE_LIST_ITEM,
                    'identities'=> $this->getIdentities(),
                    // Temporary solution.
                    // Will be revised in the scope of https://magento2.atlassian.net/browse/BB-189
                    'social_icons_block' => $this->getSocialIconsBlock()
                ]
            ]
        );
        return $block->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getPosts() as $post) {
            $identities = [\Aheadworks\Blog\Model\Post::CACHE_TAG . '_' . $post->getId()];
        }
        if ($categoryId = (int)$this->getRequest()->getParam('blog_category_id')) {
            $identities = [\Aheadworks\Blog\Model\Category::CACHE_TAG . '_' . $categoryId];
        }
        if ($tagId = (int)$this->getRequest()->getParam('tag_id')) {
            $identities = [\Aheadworks\Blog\Model\Tag::CACHE_TAG . '_'
                . $this->tagRepository->get($tagId)->getName()];
        }
        if (!$categoryId && !$tagId) {
            $identities[] = \Aheadworks\Blog\Model\Post::CACHE_TAG_LISTING;
        }
        return $identities;
    }

    /**
     * Apply pagination if needed
     */
    protected function applyPagination()
    {

        if ($this->isNeedPagination()) {
            /** @var Pager $pager */
            $pager = $this->getChildBlock('pager');
            if ($pager) {
                $pager
                    ->setPath(trim($this->getRequest()->getPathInfo(), '/'))
                    ->setLimit($this->config->getNumPostsPerPage());
                $this->postListing->applyPagination($pager);
            }
        }
    }

    /**
     * Check if there is at least one post, in this case pagination can be used
     *
     * @return int
     */
    private function isNeedPagination()
    {
        $this->postListing->getSearchCriteriaBuilder()->setPageSize(1);
        return count($this->getPosts());
    }


    /*
     *
     *
     *
     */
    protected function _beforeToHtml()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate('Aheadworks_Blog::post/list.phtml');
        }
        
        return parent::_beforeToHtml();
    }

    /**
     * Get featured image url
     *
     * @param PostInterface $post
     * @return string
     */
    public function getFeaturedImageUrl(PostInterface $post)
    {
        return $this->imageInfo->getImageUrl($post->getFeaturedImageFile());
    }

     /**
      * @param PostInterface $post
      * @return string
      */
    public function getPostUrl(PostInterface $post)
    {
        $categoryId = $this->getRequest()->getParam('blog_category_id');
        $storeId = $this->_storeManager->getStore()->getId();
        $canIncludeCategory = $this->config->getSeoUrlType($storeId) == UrlType::URL_INC_CATEGORY ? true : false;
        if ($canIncludeCategory && $categoryId) {
            return $this->url->getPostUrl($post, $this->categoryRepository->get($categoryId));
        }
        return $this->url->getPostUrl($post);
    }
    
      /**
       * Retrieve author ulr
       *
       * @return string
       */
    public function getAuthorUrl()
    {
        $author = $this->getPost()->getAuthor();

        return $this->url->getAuthorUrl($author);
    }

     /**
      * Retrieve author full name
      *
      * @return string
      */
    public function getAuthorFullname()
    {
        $author = $this->getPost()->getAuthor();

        return $author->getFirstname() . ' ' . $author->getLastname();
    }

      /**
       * Get post categories
       *
       * @return CategoryInterface[]
       */
    protected function getCategories()
    {
        $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::STATUS, CategoryStatus::ENABLED)
            ->addFilter(CategoryInterface::STORE_IDS, $this->_storeManager->getStore()->getId())
            ->addFilter(CategoryInterface::ID, $this->getPost()->getCategoryIds(), 'in');
        return $this->categoryRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();
    }
     /**
      * Retrieves array of category links html
      *
      * @return string[]
      */
    public function getCategoryLinks()
    {
        $categoryLinks = [];
        foreach ($this->getCategories() as $category) {
            /** @var Link $link */
            $link = $this->linkFactory->create();
            $categoryLinks[] = $link
                ->setHref($this->url->getCategoryUrl($category))
                ->setTitle($category->getName())
                ->setLabel($category->getName())
                ->toHtml();
        }
        return $categoryLinks;
    }

     /**
      * @param TagInterface|string $tag
      * @return string
      */
    public function getSearchByTagUrl($tag)
    {
        return $this->url->getSearchByTagUrl($tag);
    }
}
