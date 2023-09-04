<?php
namespace Aheadworks\Blog\Model\Indexer\ProductPost;

/**
 * Interface IndexManagementInterface
 */
interface IndexManagementInterface
{
    /**
     * Check if config data has been changed and reset index if necessary
     *
     * @param array $changedPaths
     */
    public function processOnConfigChanges($changedPaths);
}