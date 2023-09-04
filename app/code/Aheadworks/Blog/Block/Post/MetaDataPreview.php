<?php
namespace Aheadworks\Blog\Block\Post;

use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Model\Post\MetadataProvider;
use Aheadworks\Blog\Model\Post\Provider as PostProvider;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Blog\Api\PostPreviewManagementInterface;

/**
 * Class MetaDataPreview
 * @package Aheadworks\Blog\Block\Post
 */
class MetaDataPreview extends MetaData
{
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
     * @param MetadataProvider $metadataProvider
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PostProvider $postProvider
     * @param PostPreviewManagementInterface $postPreviewManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostRepositoryInterface $postRepository,
        MetadataProvider $metadataProvider,
        CategoryRepositoryInterface $categoryRepository,
        PostProvider $postProvider,
        PostPreviewManagementInterface $postPreviewManager,
        array $data = []
    ) {
        $this->postProvider = $postProvider;
        $this->postPreviewManager = $postPreviewManager;
        parent::__construct(
            $context,
            $postRepository,
            $metadataProvider,
            $categoryRepository,
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
