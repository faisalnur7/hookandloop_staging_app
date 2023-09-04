<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Cleaner\Delete;

/**
 * Class AbstractCleaner
 * @package Aheadworks\Blog\Model\UrlRewrites\Cleaner\Delete
 */
abstract class AbstractCleaner
{
    /**
     * @var AbstractCleaner[]
     */
    private $subordinateEntitiesCleaners;

    /**
     * AbstractCleaner constructor.
     * @param array $subordinateEntitiesCleaners
     */
    public function __construct(
        $subordinateEntitiesCleaners = []
    ) {
        $this->subordinateEntitiesCleaners = $subordinateEntitiesCleaners;
    }

    /**
     * Clean rewrites for entity
     *
     * @param mixed $deletedEntity
     * @return void
     */
    public function clean($deletedEntity)
    {
        $this->cleanEntityRewrites($deletedEntity);
        $this->cleanSubordinateEntitiesRewrites($deletedEntity);
    }

    /**
     * Clean rewrites
     *
     * @param mixed $deletedEntity
     */
    abstract protected function cleanEntityRewrites($deletedEntity);

    /**
     * Clean rewrites for subordinate entities
     *
     * @param mixed $deletedEntity
     * @return void
     */
    private function cleanSubordinateEntitiesRewrites($deletedEntity)
    {
        /** @var AbstractCleaner $cleaner */
        foreach ($this->subordinateEntitiesCleaners as $cleaner) {
            $cleaner->clean($deletedEntity);
        }
    }
}
