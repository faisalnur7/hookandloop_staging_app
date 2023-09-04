<?php
namespace Aheadworks\Blog\Model\Rule\Product\Collection\Preparer;

use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface;
use Magento\Framework\Exception\ConfigurationMismatchException;

class SortedPool
{
    /**
     * @var Factory
     */
    private $preparerFactory;

    /**
     * @var array
     */
    private $preparerDataList = [];

    /**
     * @var PreparerInterface[]
     */
    private $preparerInstanceList = [];

    /**
     * @param Factory $preparerFactory
     * @param array $preparerDataList
     */
    public function __construct(
        Factory $preparerFactory,
        array $preparerDataList = []
    ) {
        $this->preparerFactory = $preparerFactory;
        $this->preparerDataList = $this->sort($preparerDataList);
    }

    /**
     * Retrieve the sorted list of preparer instances
     *
     * @return PreparerInterface[]
     * @throws ConfigurationMismatchException
     */
    public function getInstanceList()
    {
        if ($this->preparerInstanceList) {
            return $this->preparerInstanceList;
        }

        foreach ($this->preparerDataList as $preparerData) {
            if (empty($preparerData['class'])) {
                throw new ConfigurationMismatchException(
                    __('The parameter "class" is missing. Set the "class" and try again.')
                );
            }

            if (empty($preparerData['sortOrder'])) {
                throw new ConfigurationMismatchException(
                    __('The parameter "sortOrder" is missing. Set the "sortOrder" and try again.')
                );
            }

            $this->preparerInstanceList[$preparerData['class']] = $this->preparerFactory->create(
                $preparerData['class']
            );
        }

        return $this->preparerInstanceList;
    }

    /**
     * Sorting preparer data list according to the sort order
     *
     * @param array $preparerDataList
     * @return array
     */
    private function sort(array $preparerDataList)
    {
        usort($preparerDataList, function (array $firstPreparerData, array $secondPreparerData) {
            return $this->getSortOrder($firstPreparerData) <=> $this->getSortOrder($secondPreparerData);
        });

        return $preparerDataList;
    }

    /**
     * Retrieve sort order from preparer data array
     *
     * @param array $preparerData
     * @return int
     */
    private function getSortOrder(array $preparerData)
    {
        return $preparerData['sortOrder'] ?? 0;
    }
}
