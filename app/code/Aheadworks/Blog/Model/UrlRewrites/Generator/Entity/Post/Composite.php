<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator;

/**
 * Class Composite
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Post
 */
class Composite extends AbstractGenerator
{
    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        return [];
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     */
    protected function getControllerRewrites($storeId, $newEntityState)
    {
       return [];
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function getExistingRewrites($storeId, $newEntityState, $oldEntityState)
    {
        return [];
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface|null $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
        return true;
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        return true;
    }
}
