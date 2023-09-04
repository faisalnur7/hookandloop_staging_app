<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider;

use Aheadworks\BlogSearch\Ui\DataProvider\ItemModifier\ModifierInterface;
use Aheadworks\BlogSearch\Ui\DataProvider\Filter\Applier as FilterApplier;
use Magento\Framework\Api\Filter;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Class AbstractDataProvider
 */
abstract class AbstractDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var ModifierInterface
     */
    private $itemModifier;

    /**
     * @var AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategyList;

    /**
     * AbstractDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param FilterApplier $filterApplier
     * @param array $addFilterStrategyList
     * @param array $meta
     * @param array $data
     * @param ModifierInterface|null $itemModifier
     * @throws ConfigurationMismatchException
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        FilterApplier $filterApplier,
        array $addFilterStrategyList = [],
        array $meta = [],
        array $data = [],
        ModifierInterface $itemModifier = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->itemModifier = $itemModifier;
        $this->addFilterStrategyList = $addFilterStrategyList;

        $filterApplier->apply($this);
    }

    /**
     * Returns list of data provider items
     *
     * @return mixed[]
     */
    public function getItems()
    {
        $rawItems = $this->getCollection()->getItems();

        $preparedItems = [];
        foreach ($rawItems as $rawItem) {
            $preparedItems[] = $this->itemModifier ? $this->itemModifier->modify($rawItem) : $rawItem;
        }

        return $preparedItems;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        if (isset($this->addFilterStrategyList[$filter->getField()])) {
            $this->addFilterStrategyList[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [
                        $filter->getConditionType() => $filter->getValue()
                    ]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
