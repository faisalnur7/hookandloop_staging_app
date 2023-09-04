<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update;

/**
 * Class AbstractCleaner
 * @package Aheadworks\Blog\Model\UrlRewrites\Cleaner\Update
 */
abstract class AbstractCleaner
{
    /**
     * @var AbstractCleaner[]
     */
    private $subordinateEntitiesCleaners;

    /**
     * AbstractCleaner constructor.
     * @param AbstractCleaner[] $subordinateEntitiesCleaners
     */
    public function __construct(
        $subordinateEntitiesCleaners = []
    ) {
        $this->subordinateEntitiesCleaners = $subordinateEntitiesCleaners;
    }

    /**
     * Clean rewrites for entity
     *
     * @param mixed $newEntityState
     * @param mixed $oldEntityState
     * @return void
     */
    public function clean($newEntityState, $oldEntityState)
    {
        $this->cleanEntityRewrites($newEntityState, $oldEntityState);
        $this->cleanSubordinateEntitiesRewrites($newEntityState, $oldEntityState);
    }

    /**
     * Clean rewrites
     *
     * @param mixed $newEntityState
     * @param mixed $oldEntityState
     * @return void
     */
    abstract protected function cleanEntityRewrites($newEntityState, $oldEntityState);

    /**
     * Clean rewrites for subordinate entities
     *
     * @param mixed $newEntityState
     * @param mixed $oldEntityState
     * @return void
     */
    private function cleanSubordinateEntitiesRewrites($newEntityState, $oldEntityState)
    {
        /** @var AbstractCleaner $cleaner */
        foreach ($this->subordinateEntitiesCleaners as $cleaner) {
            $cleaner->clean($newEntityState, $oldEntityState);
        }
    }
}
