<?php

namespace VladimirPopov\WebForms\Model\ResourceModel\Result\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\AggregationInterface;
use VladimirPopov\WebForms\Model\ResourceModel\Result\Collection as ResultCollection;

/**
 * Collection for displaying grid of cms blocks.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends ResultCollection implements SearchResultInterface
{

    /** @var \Magento\Framework\App\RequestInterface */
    protected $request;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Collection constructor.
     * @param \VladimirPopov\WebForms\Model\FieldFactory $fieldFactory
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param $mainTable
     * @param $eventPrefix
     * @param $eventObject
     * @param $resourceModel
     * @param string $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \VladimirPopov\WebForms\Model\FieldFactory $fieldFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        $this->request = $request;

        parent::__construct($fieldFactory, $entityFactory, $logger, $fetchStrategy, $eventManager, $customerFactory, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->_loadValues     = false;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     *
     * @return void
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|bool
     */
    public function getSearchCriteria()
    {
        return false;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[]|array $items
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $webform_id = $this->request->getParam('webform_id');

        if ($webform_id) {
            $fields = $this->_fieldFactory->create()->getCollection()->addFilter('webform_id', $webform_id);
            if (count($fields) < 61) {
                foreach ($fields as $field) {
                    $result_values = $this->getResource()->getConnection()->select()
                        ->from($this->getTable('webforms_results_values'), ['result_id', 'value'])
                        ->where("field_id = {$field->getId()}");
                    $this->getSelect()->joinLeft(
                        ['results_values_' . $field->getId() => $result_values],
                        'main_table.id = results_values_' . $field->getId() . '.result_id',
                        ["field_{$field->getId()}" => "results_values_{$field->getId()}.value"]
                    );
                }
            }
        }
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        $webform_id = $this->request->getParam('webform_id');
        if ($webform_id) {
            $fields = $this->_fieldFactory->create()->getCollection()->addFilter('webform_id', $webform_id);
            if (count($fields) > 60) {
                foreach ($this as $item) {
                    if ($item->getData('id')) {
                        $query   = $this->getConnection()->select()
                            ->from($this->getTable('webforms_results_values'))
                            ->where($this->getTable('webforms_results_values') . '.result_id = ' . $item->getData('id'));
                        $results = $this->getConnection()->fetchAll($query);
                        foreach ($results as $result) {
                            $item->setData('field_' . $result['field_id'], trim($result['value']));
                            $item->setData('key_' . $result['field_id'], $result['key']);
                        }
                    }
                    $item->setData('ip', long2ip($item->getCustomerIp()));

                }
            }
        }

        $this->_eventManager->dispatch('webforms_results_collection_load', array('collection' => $this));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'customer') {
            // custom condition
            $value = substr($condition['like'], 1, -1);
            while (strstr($value, "  ")) {
                $value = str_replace("  ", " ", $value);
            }
            $customers_array = [];

            $name      = explode(" ", $value);
            $firstname = $name[0];

            $lastname = $name[count($name) - 1];

            $customers = $this->customerFactory->create()->getCollection()
                ->addAttributeToFilter('firstname', $firstname);
            if (count($name) == 2)
                $customers->addAttributeToFilter('lastname', $lastname);
            foreach ($customers as $customer) {
                $customers_array[] = $customer->getId();
            }
            return $this->_addFieldToFilter('customer_id', ['in' => $customers_array]);
        }

        if (isset($condition['gteq']) && !$this->validateDate($condition['gteq'])) $condition['gteq'] = floatval($condition['gteq']);
        if (isset($condition['lteq']) && !$this->validateDate($condition['lteq'])) $condition['lteq'] = floatval($condition['lteq']);

        if (is_string($field) && preg_match('/results_values_(\d+).value/', $field, $matches)) {
            $resultCondition = $this->getResultValueCondition((int)$matches[1], $condition);
            $this->_select->where($resultCondition, null, Select::TYPE_CONDITION);
            return $this;
        }
        return $this->_addFieldToFilter($field, $condition);
    }

    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function _addFieldToFilter($field, $condition = null)
    {
        if (is_array($field)) {
            $conditions = [];
            foreach ($field as $key => $value) {
                $conditions[] = $this->_translateCondition($value, isset($condition[$key]) ? $condition[$key] : null);
            }

            $resultCondition = '(' . implode(') ' . Select::SQL_OR . ' (', $conditions) . ')';
        } else {
            $resultCondition = $this->_translateCondition($field, $condition);
        }

        $this->_select->where($resultCondition, null, Select::TYPE_CONDITION);

        return $this;
    }

    /**
     * @param int|string $fieldId
     * @param null|string|array $condition
     * @return string
     */
    protected function getResultValueCondition($fieldId, $condition)
    {
        $resultCondition = $this->_translateCondition('webforms_results_values.value', $condition);
        $query  = $this->getConnection()->select()
            ->from($this->getTable('webforms_results_values'),
                ['COUNT(webforms_results_values.id)'])
            ->where('webforms_results_values.result_id = main_table.id')
            ->where('webforms_results_values.field_id = ?', $fieldId)
            ->where($resultCondition, null, Select::TYPE_CONDITION);
        return '(' . $query->__toString() . ') > 0';
    }
}
