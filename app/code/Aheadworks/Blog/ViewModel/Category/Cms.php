<?php
namespace Aheadworks\Blog\ViewModel\Category;

use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Cms\CmsBlockProvider;
use Aheadworks\Blog\Model\Image\Info as ImageInfo;
use Aheadworks\Blog\Model\Template\FilterProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Cms
 */
class Cms extends Details
{
    /**
     * @var CmsBlockProvider
     */
    private $cmsBlockProvider;

    /**
     * Cms constructor.
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ImageInfo $imageInfo
     * @param FilterProvider $templateFilterProvider
     * @param StoreManagerInterface $storeManager
     * @param CmsBlockProvider $cmsBlockProvider
     */
    public function __construct(
        RequestInterface $request,
        CategoryRepositoryInterface $categoryRepository,
        ImageInfo $imageInfo,
        FilterProvider $templateFilterProvider,
        StoreManagerInterface $storeManager,
        CmsBlockProvider $cmsBlockProvider
    ) {
        $this->cmsBlockProvider = $cmsBlockProvider;
        parent::__construct(
            $request,
            $categoryRepository,
            $imageInfo,
            $templateFilterProvider,
            $storeManager
        );
    }

    /**
     * Retrieve Cms block id
     *
     * @return int|null
     */
    public function getCmsBlockId()
    {
        return $this->getCurrentCategory()->getCmsBlockId();
    }

    /**
     * Retrieve cms block identifier
     *
     * @return string|null
     */
    public function getCmsBlockIdentifier()
    {
        $cmsBlockId = $this->getCmsBlockId();
        $storeId = $this->storeManager->getStore()->getId();
        $cmsBlock = $this->cmsBlockProvider->getCmsBlockById($cmsBlockId, $storeId);

        return $cmsBlock ? $cmsBlock->getIdentifier() : null;
    }

    /**
     * Render cms block
     *
     * @param AbstractBlock $block
     * @return string
     */
    public function renderCmsBlock($block)
    {
        $identifier = $this->getCmsBlockIdentifier();
        $cmsBlockRenderer = $block->getChildBlock('category.cms.renderer');

        return $cmsBlockRenderer && $identifier
            ? $cmsBlockRenderer->setBlockId($identifier)->toHtml()
            : '';
    }

    /**
     * Check if need to display Cms block for specific category
     *
     * @param CategoryInterface $currentCategory
     * @return bool
     */
    public function isNeedToDisplayCmsBlock($currentCategory)
    {
        return $this->isStaticOnlyMode($currentCategory) || $this->isMixedMode($currentCategory);
    }
}
