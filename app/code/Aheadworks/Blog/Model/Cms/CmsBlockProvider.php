<?php
namespace Aheadworks\Blog\Model\Cms;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\Block;

/**
 * Class CmsBlockProvider
 */
class CmsBlockProvider
{
    /**
     * @var BlockFactory
     */
    private $cmsBlockFactory;

    /**
     * CmsBlockProvider constructor.
     * @param BlockFactory $cmsBlockFactory
     */
    public function __construct(
        BlockFactory $cmsBlockFactory
    ) {
        $this->cmsBlockFactory = $cmsBlockFactory;
    }

    /**
     * Retrieves CMS block by id
     *
     * @param int $cmsBlockId
     * @param int $storeId
     * @return Block|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCmsBlockById($cmsBlockId, $storeId)
    {
        if ($cmsBlockId) {
            $cmsBlock = $this->cmsBlockFactory->create()
                ->setStoreId($storeId)
                ->load($cmsBlockId);
        }

        return $cmsBlock ?? null;
    }
}