<?php
namespace Aheadworks\Blog\Model\Rule;

use Magento\CatalogRule\Model\Rule\Condition\CombineFactory;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory as ActionCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Model\ResourceModel\Iterator as ResourceIterator;
use Magento\Catalog\Model\ProductFactory as ProductModelFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\CatalogRule\Helper\Data as RuleHelperData;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface as ProductCollectionPreparerInterface;

/**
 * Class Product
 *
 * @package Aheadworks\Blog\Model\Rule
 */
class Product extends \Magento\CatalogRule\Model\Rule
{
    /**
     * @var CombineFactory
     */
    private $combineFactory;

    /**
     * @var ActionCollectionFactory
     */
    private $actionCollectionFactory;

    /**
     * @var ProductCollectionPreparerInterface
     */
    private $productCollectionPreparer;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $combineFactory
     * @param ActionCollectionFactory $actionCollectionFactory
     * @param ProductCollectionPreparerInterface $productCollectionPreparer
     * @param ProductModelFactory $productFactory
     * @param ResourceIterator $resourceIterator
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param RuleHelperData $catalogRuleData
     * @param TypeListInterface $cacheTypesList
     * @param DateTime $dateTime
     * @param RuleProductProcessor $ruleProductProcessor
     * @param CollectionFactory $catalogProductCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        ActionCollectionFactory $actionCollectionFactory,
        ProductCollectionPreparerInterface $productCollectionPreparer,
        ProductModelFactory $productFactory,
        ResourceIterator $resourceIterator,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        RuleHelperData $catalogRuleData,
        TypeListInterface $cacheTypesList,
        DateTime $dateTime,
        RuleProductProcessor $ruleProductProcessor,
        CollectionFactory $catalogProductCollectionFactory,
        array $data = []
    ) {
        $this->combineFactory = $combineFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        $this->productCollectionPreparer = $productCollectionPreparer;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $catalogProductCollectionFactory,
            $storeManager,
            $combineFactory,
            $actionCollectionFactory,
            $productFactory,
            $resourceIterator,
            $customerSession,
            $catalogRuleData,
            $cacheTypesList,
            $dateTime,
            $ruleProductProcessor,
            null,
            null,
            [],
            $data
        );
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Aheadworks\Blog\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    /**
     * Reset rule combine conditions
     *
     * @param \Aheadworks\Blog\Model\Rule\Condition\Combine|null $conditions
     * @return $this
     */
    protected function _resetConditions($conditions = null)
    {
        parent::_resetConditions($conditions);
        $this->getConditions($conditions)
            ->setId('1')
            ->setPrefix('conditions');
        return $this;
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @param array $productIdsFilter
     * @return array
     * @throws LocalizedException
     */
    public function getProductIds($productIdsFilter = [])
    {
        $this->_productIds = [];
        $this->setCollectedAttributes([]);

        if ($this->getWebsiteIds()) {
            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->_productCollectionFactory->create();

            $productCollection = $this->productCollectionPreparer->prepare(
                $productCollection,
                [
                    ProductCollectionPreparerInterface::CONDITIONS_KEY => $this->getConditions(),
                    ProductCollectionPreparerInterface::PRODUCTS_FILTER_KEY => $productIdsFilter,
                    ProductCollectionPreparerInterface::WEBSITE_IDS_KEY => $this->getWebsiteIds(),
                    ProductCollectionPreparerInterface::IS_FILTER_VISIBILITY_ENABLED => true
                ]
            );

            $this->_resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->_productFactory->create()
                ]
            );
        }

        return $this->_productIds;
    }
}
