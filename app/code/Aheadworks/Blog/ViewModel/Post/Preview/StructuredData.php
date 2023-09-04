<?php
namespace Aheadworks\Blog\ViewModel\Post\Preview;

use Aheadworks\Blog\ViewModel\Post\StructuredData as PostStructuredData;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Model\Post\StructuredData\ProviderInterface as PostStructuredDataProviderInterface;
use Aheadworks\Blog\Model\Post\Provider as PostProvider;
use Aheadworks\Blog\Api\PostPreviewManagementInterface;

class StructuredData extends PostStructuredData
{

    /**
     * @param RequestInterface $request
     * @param SerializerInterface $serialize
     * @param PostRepositoryInterface $postRepository
     * @param PostStructuredDataProviderInterface $postStructuredDataProvider
     * @param PostProvider $postProvider
     * @param PostPreviewManagementInterface $postPreviewManager
     */
    public function __construct(
        private readonly RequestInterface $request,
        SerializerInterface $serialize,
        PostRepositoryInterface $postRepository,
        PostStructuredDataProviderInterface $postStructuredDataProvider,
        protected readonly PostProvider $postProvider,
        private readonly PostPreviewManagementInterface $postPreviewManager
    ) {
        parent::__construct(
            $request,
            $serialize,
            $postRepository,
            $postStructuredDataProvider
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getCurrentPost()
    {
        $key = $this->request->getParam('data');
        $postData = $this->postPreviewManager->withdrawPreviewData($key);
        $currentPost = $this->postProvider->getByData($postData);

        return $currentPost;
    }
}
