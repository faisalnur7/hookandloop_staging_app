<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\Entity\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update\AbstractCleaner;

/**
 * Class Composite
 * @package Aheadworks\Blog\Model\UrlRewrites\Cleaner\Entity\Post
 */
class Composite extends AbstractCleaner
{
    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function cleanEntityRewrites($newEntityState, $oldEntityState)
    {
    }
}
