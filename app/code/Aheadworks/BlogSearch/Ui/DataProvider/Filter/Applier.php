<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider\Filter;

use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;

/**
 * Class Applier
 */
class Applier
{
    /**
     * @var BuilderInterface[]
     */
    private $filterBuilderList;

    /**
     * @param BuilderInterface[] $filterBuilderList
     */
    public function __construct(array $filterBuilderList = [])
    {
        $this->filterBuilderList = $filterBuilderList;
    }

    /**
     * Apply filters by builders list to the data provider
     *
     * @param DataProviderInterface $dataProvider
     * @return DataProviderInterface
     * @throws ConfigurationMismatchException
     */
    public function apply($dataProvider)
    {
        foreach ($this->filterBuilderList as $filterBuilder) {
            if (!$filterBuilder instanceof BuilderInterface) {
                throw new ConfigurationMismatchException(
                    __('Filter builder must implement %1', BuilderInterface::class)
                );
            }
            $filter = $filterBuilder->build();
            if ($filter) {
                $dataProvider->addFilter($filter);
            }
        }
        return $dataProvider;
    }
}
