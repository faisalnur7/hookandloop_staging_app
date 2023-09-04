<?php
namespace Aheadworks\Blog\Model\UrlRewrites;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;

/**
 * Class responsible for updating url rewrites
 *
 * Class RewriteUpdater
 * @package Aheadworks\Blog\Model\UrlRewrites
 */
class RewriteUpdater
{
    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * RewriteUpdater constructor.
     * @param RewriteStorageInterface $rewriteStorageInterface
     */
    public function __construct(
        RewriteStorageInterface $rewriteStorage
    ) {
        $this->rewriteStorage = $rewriteStorage;
    }

    /**
     * @param UrlRewrite[] $urls
     */
    public function update(array $urls)
    {
        $this->rewriteStorage->replace($urls);
    }
}
