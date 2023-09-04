<?php
namespace Aheadworks\Blog\Model\Indexer\ProductPost;

use Aheadworks\Blog\Model\Config;

/**
 * Class ConfigChecker
 */
class ConfigChecker
{
    /**
     * @var array
     */
    private $configPathsForVerification = [
        Config::XML_PATH_RELATED_DISPLAY_POSTS_ON_PRODUCT_PAGE,
        Config::XML_PATH_RELATED_BLOCK_POSITION,
        Config::XML_PATH_RELATED_PRODUCTS_LIMIT
    ];

    /**
     * ConfigChecker constructor.
     * @param array $configPathsForVerification
     */
    public function __construct(
        $configPathsForVerification = []
    ) {
        $this->configPathsForVerification = array_merge($this->configPathsForVerification, $configPathsForVerification);
    }

    /**
     * Check if config for verification has been changed
     *
     * @param array $changedPaths
     */
    public function isChanged($changedPaths)
    {
        return (bool)array_intersect(
            $changedPaths,
            $this->configPathsForVerification
        );
    }
}
