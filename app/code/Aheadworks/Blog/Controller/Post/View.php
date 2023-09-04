<?php
namespace Aheadworks\Blog\Controller\Post;

use Aheadworks\Blog\Api\AuthorRepositoryInterface;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Api\TagRepositoryInterface;
use Aheadworks\Blog\Controller\Action;
use Aheadworks\Blog\Controller\Checker;
use Aheadworks\Blog\Controller\Checker as DataChecker;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Resolver\Title as TitleResolver;
use Aheadworks\Blog\Model\Url;
use Aheadworks\Blog\Model\Url\TypeResolver as UrlTypeResolver;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Blog\Model\Post\Author\Resolver as PostAuthorResolver;
use Aheadworks\Blog\Model\Seo\CanonicalIncluder;

/**
 * Class View
 * @package Aheadworks\Blog\Controller\Post
 */
class View extends Action
{
    /**
     * @var PostAuthorResolver
     */
    private $postAuthorResolver;

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
     * @param PostAuthorResolver $postAuthorResolver
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
        CanonicalIncluder $canonicalIncluder,
        PostAuthorResolver $postAuthorResolver
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultForwardFactory,
            $storeManager,
            $coreRegistry,
            $categoryRepository,
            $postRepository,
            $tagRepository,
            $authorRepository,
            $config,
            $url,
            $urlTypeResolver,
            $dataChecker,
            $titleResolver,
            $canonicalIncluder
        );
        $this->postAuthorResolver = $postAuthorResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        if ($postId = (int)$this->getRequest()->getParam('post_id')) {
            try {
                $post = $this->postRepository->get($postId);
                if (!$this->dataChecker->isPostVisible($post, $this->getStoreId())) {
                    return $this->noRoute();
                }
                $categoryId = $this->getRequest()->getParam('blog_category_id');
                $exclCategoryFromUrl = $this->urlTypeResolver->isCategoryExcl() && $categoryId ? true : false;

                if ($exclCategoryFromUrl
                    || (
                        $categoryId
                        && is_array($post->getCategoryIds())
                        && !in_array($categoryId, $post->getCategoryIds())
                    )
                ) {
                    // Forced redirect to post url without category id
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setUrl($this->url->getPostUrl($post));
                    return $resultRedirect;
                }

                $resultPage = $this->resultPageFactory->create();
                $pageConfig = $resultPage->getConfig();
                $resultPage->addPageLayoutHandles(['id' => $postId]);

                if ($this->areMetaTagsEnabled()) {
                    $pageConfig->getTitle()->set($this->titleResolver->getTitle($post));
                    if ($post->getMetaKeywords()) {
                        $pageConfig->setMetadata('keywords', $post->getMetaKeywords());
                    }
                    if ($post->getMetaDescription()) {
                        $pageConfig->setMetadata('description', $post->getMetaDescription());
                    }
                    $authorFullName = $this->postAuthorResolver->getFullName($post);
                    if (!empty($authorFullName)) {
                        $pageConfig->setMetadata('author', $authorFullName);
                    }
                    if ($this->config->getPostCanonicalTag()) {
                        $pageConfig->addRemotePageAsset(
                            $this->url->getCanonicalUrl($post),
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
