<?php
namespace Aheadworks\BlogSearch\Model;

use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class RewriteFriendlyUrlFinder
 */
class RewriteFriendlyUrlFinder
{
    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * RewriteFriendlyUrlFinder constructor.
     * @param RewriteStorageInterface $rewriteStorage
     */
    public function __construct(
        RewriteStorageInterface $rewriteStorage
    ) {
        $this->rewriteStorage = $rewriteStorage;
    }

    /**
     * Returns friendly url for controller path from url rewrites if exists
     *
     * @param string $controllerPath
     * @param int $storeId
     * @returns string|null null if not found
     */
    public function getFriendlyUrl($controllerPath, $storeId)
    {
        /** @var UrlRewrite[] $existingRewrites */
        $existingRewrites = $this->rewriteStorage->findAllByData([
            UrlRewrite::TARGET_PATH => $controllerPath,
            UrlRewrite::STORE_ID => $storeId
        ]);

        $result = null;
        foreach ($existingRewrites as $existingRewrite) {
            $isControllerRewrite = empty($existingRewrite->getRedirectType());
            if ($isControllerRewrite){
                $result = $existingRewrite->getRequestPath();
            }
        }

        return $result;
    }
}
