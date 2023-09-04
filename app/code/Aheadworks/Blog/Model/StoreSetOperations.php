<?php
namespace Aheadworks\Blog\Model;

/**
 * Class StoreSetOperations
 * @package Aheadworks\Blog\Model
 */
class StoreSetOperations
{
    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * StoreSetOperations constructor.
     * @param \Aheadworks\Blog\Model\StoreResolver $storeResolver
     */
    public function __construct(
        StoreResolver $storeResolver
    ) {
        $this->storeResolver = $storeResolver;
    }

    /**
     * Returns store sets intersection
     *
     * @param int[] $storeSetOne
     * @param int[] $storeSetTwo
     * @return int[]
     */
    public function getIntersection($storeSetOne, $storeSetTwo)
    {
        $storeSetOne = $this->extractRealStores($storeSetOne);
        $storeSetTwo = $this->extractRealStores($storeSetTwo);

        return array_intersect($storeSetOne, $storeSetTwo);
    }

    /**
     * Returns difference between stores sets
     *
     * @param int[] $decreasedSet
     * @param int[] $subtractedSet
     * @return int[]
     */
    public function getDifference($decreasedSet, $subtractedSet)
    {
        $decreasedSet = $this->extractRealStores($decreasedSet);
        $subtractedSet = $this->extractRealStores($subtractedSet);

        return array_diff($decreasedSet, $subtractedSet);
    }

    /**
     * Checks if 2 store sets equal
     *
     * @param int[] $storeSetOne
     * @param int[] $storeSetTwo
     * @return bool
     */
    public function isEqual($storeSetOne, $storeSetTwo)
    {
        $storeSetOne = $this->extractRealStores($storeSetOne);
        $storeSetTwo = $this->extractRealStores($storeSetTwo);

        return count($storeSetOne) == count($storeSetTwo)
            && array_diff($storeSetOne, $storeSetTwo) === array_diff($storeSetTwo, $storeSetOne);
    }

    /**
     * Extracts real stores from set(for example all store views if store = 0 in set)
     *
     * @param int[] $storeSet
     * @return int[]
     */
    public function extractRealStores($storeSet)
    {
        return in_array(StoreResolver::ALL_STORE_VIEWS, $storeSet)
            ? $this->storeResolver->getAllStoreIds()
            : $storeSet;
    }
}
