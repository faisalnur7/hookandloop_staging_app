<?php
namespace Aheadworks\Blog\Block;

use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Api\TagManagementInterface as TagManagement;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Post\FeaturedImageInfo;
use Aheadworks\Blog\Model\Post\Provider as PostProvider;
use Aheadworks\Blog\Model\Template\FilterProvider;
use Aheadworks\Blog\Model\Url;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Blog\Api\PostPreviewManagementInterface;

/**
 * Class PostPreview
 */
class PostPreview extends Post
{
    /**
     * @var string
     */
    protected $_template = 'postpreview.phtml';

    /**
     * @var PostProvider
     */
    private $postProvider;

    /**
     * @var PostPreviewManagementInterface
     */
    private $postPreviewManager;

    /**
     * @param Context $context
     * @param PostRepositoryInterface $postRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     * @param LinkFactory $linkFactory
     * @param Url $url
     * @param FilterProvider $templateFilterProvider
     * @param FeaturedImageInfo $imageInfo
     * @param TagManagement $tagManagement
     * @param PostProvider $postProvider
     * @param PostPreviewManagementInterface $postPreviewManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostRepositoryInterface $postRepository,
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config,
        LinkFactory $linkFactory,
        Url $url,
        FilterProvider $templateFilterProvider,
        FeaturedImageInfo $imageInfo,
        TagManagement $tagManagement,
        PostProvider $postProvider,
        PostPreviewManagementInterface $postPreviewManager,
        array $data = []
    ) {
        $this->postProvider = $postProvider;
        $this->postPreviewManager = $postPreviewManager;
        parent::__construct(
            $context, $postRepository,
            $categoryRepository,
            $searchCriteriaBuilder,
            $config,
            $linkFactory,
            $url,
            $templateFilterProvider,
            $imageInfo,
            $tagManagement,
            $data
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $key = $this->getRequest()->getParam('data');
        $postData = $this->postPreviewManager->getPreviewData($key);
        $post = $this->postProvider->getByData($postData);

        $this->setPost($post);
    }
}
