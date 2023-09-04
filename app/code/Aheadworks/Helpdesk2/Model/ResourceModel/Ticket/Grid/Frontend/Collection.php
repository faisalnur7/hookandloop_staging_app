<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Frontend;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Collection as TicketCollection;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;

class Collection extends TicketCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var array
     */
    private $orFilterList = [
        TicketInterface::CUSTOMER_ID,
        TicketInterface::CUSTOMER_EMAIL
    ];

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Document::class, TicketGridResourceModel::class);
    }
    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this
            ->getSelect()
            ->joinLeft(
                [Ticket::TICKET_ENTITY_TABLE_NAME => $this->getTable(Ticket::TICKET_ENTITY_TABLE_NAME)],
                'main_table.' . TicketInterface::ENTITY_ID . ' = ' .
                Ticket::TICKET_ENTITY_TABLE_NAME . '.' . TicketInterface::ENTITY_ID,
                [TicketInterface::STORE_ID]
            );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = $this->processAddFieldToFilter($field, $condition);

        if (!empty($fieldsToProcess)) {
            return parent::addFieldToFilter($fieldsToProcess, $condition);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Set Aggregation
     *
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Process adding fields to filter
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return array|string
     */
    private function processAddFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = null;
        if (is_array($field)) {
            $fieldsToProcess = [];
            foreach ($field as $fieldName) {
                if (in_array($fieldName, $this->orFilterList)) {
                    $this->addOrFilter($fieldName, $condition);
                } else {
                    $fieldsToProcess[] = $fieldName;
                }
            }
        } else {
            if (in_array($field, $this->orFilterList)) {
                $this->addOrFilter($field, $condition);
            } else {
                $fieldsToProcess = $field;
            }
        }

        return $fieldsToProcess;
    }

    /**
     * Add or filter
     *
     * @param string $field
     * @param array $condition
     * @return $this
     */
    private function addOrFilter($field, $condition)
    {
        $resultCondition = $this->_translateCondition($field, $condition);
        $this->_select->orWhere($resultCondition, null, Select::TYPE_CONDITION);

        return $this;
    }
}
