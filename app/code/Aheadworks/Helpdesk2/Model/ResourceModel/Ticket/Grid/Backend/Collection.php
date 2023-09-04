<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Backend;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Collection as TicketCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResource;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Relation\Loader as TagLoader;
use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Psr\Log\LoggerInterface;

class Collection extends TicketCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var bool
     */
    private static $allTags = false;

    /**
     * @var array
     */
    private $joinColumns = [
        'department_id' => 'main_table.department_id',
        'agent_id' => 'main_table.agent_id',
        'customer_rating' => Ticket::TICKET_ENTITY_TABLE_NAME.'.'.TicketInterface::CUSTOMER_RATING,
        'customer_group_id' => 'customer_group_ticket.group_id'
    ];

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param PermissionManager $permissionManager
     * @param TagLoader $tagLoader
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        private readonly PermissionManager $permissionManager,
        private readonly TagLoader $tagLoader,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

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
        $this->permissionManager->applyAgentFilterToTicketCollection($this);
        $this->attachCustomerGroupId();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $process = function ($field) use ($condition){
            if (isset($this->joinColumns[$field])) {
                $this->addFilterToMap($field, $this->joinColumns[$field]);
            } elseif ($field === TicketInterface::TAG_NAMES) {
                $this->addTagNamesFilter($field, $condition);
            } elseif ($field === TicketInterface::FILTER_ALL_TAGS && $condition['eq']) {
                self::$allTags = true;
            } else {
                $this->addFilterToMap($field, 'main_table.' . $field);
            }
        };

        if (is_array($field)) {
            array_walk($field, $process);
        } else {
            $process($field);
        }
        if ($field === TicketInterface::FILTER_ALL_TAGS || ($field === TicketInterface::TAG_NAMES && self::$allTags)) {
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add tag_names filter
     *
     * @param string $field
     */
    private function addTagNamesFilter($field, $condition)
    {
        $this
            ->getSelect()
            ->joinLeft(
                ['ticket_tag_table' => $this->getTable(TagResource::RELATION_TABLE_NAME)],
                'main_table.entity_id = ticket_tag_table.ticket_id',
                []
            )
            ->group('main_table.entity_id');
        if (self::$allTags && !empty($condition['in'])) {
            $where = [];
            foreach ($condition['in'] as $tag) {
                $uid = uniqid("ticket_tag_table");
                $this
                    ->getSelect()
                    ->joinLeft(
                        [$uid => $this->getTable(TagResource::RELATION_TABLE_NAME)],
                        sprintf(
                            'main_table.entity_id = %s.ticket_id and %s.tag_id = %s',
                            $uid,
                            $uid,
                            $tag
                        ),
                        []
                    );
                $where[] = sprintf("%s.ticket_id = main_table.entity_id", $uid);
            }
            $this->getSelect()->where(implode(' and ', $where));
        }
        $this->addFilterToMap($field, 'ticket_tag_table.tag_id');
    }
    /**
     * Add tag_names filter
     *
     * @param string $field
     */
    private function addCustomerRatingFilter($field)
    {
        $this
            ->getSelect()
            ->joinLeft(
                [Ticket::TICKET_ENTITY_TABLE_NAME => $this->getTable(Ticket::TICKET_ENTITY_TABLE_NAME)],
                'main_table.'.TicketInterface::ENTITY_ID. ' = ' .
                Ticket::TICKET_ENTITY_TABLE_NAME.'.'.TicketInterface::ENTITY_ID,
                []
            );
        $this->addFilterToMap($field, Ticket::TICKET_ENTITY_TABLE_NAME.'.'.TicketInterface::ENTITY_ID);
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
     * @inheritdoc
     *
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->attachTagNames();
        return parent::_afterLoad();
    }

    /**
     * Attach tag names to collection items
     *
     * @return void
     * @throws \Exception
     */
    private function attachTagNames()
    {
        $ids = $this->getColumnValues(TicketInterface::ENTITY_ID);
        if (count($ids)) {
            $tags = $this->tagLoader->loadForManyTicket($ids);
            foreach ($this as $item) {
                $ticketId = $item->getData(TicketInterface::ENTITY_ID);
                if (isset($tags[$ticketId])) {
                    $item->setData(TicketInterface::TAG_NAMES, implode(', ', $tags[$ticketId]));
                }
            }
        }
    }

    /**
     * Attach Customer Group Id
     *
     * @return void
     */
    private function attachCustomerGroupId(): void
    {
        $groupSubQuery = $this->getConnection()->select()
            ->from(
                ['awhtg' => $this->getTable('aw_helpdesk2_ticket_grid')],
                ['entity_id']
            )->joinLeft(
                ['ce' => $this->getTable('customer_entity')],
                'awhtg.' . TicketInterface::CUSTOMER_ID . ' = ce.entity_id',
                'COALESCE(ce.group_id, ' . CustomerGroup::NOT_LOGGED_IN_ID . ') AS group_id'
            );
        $this->getSelect()->joinLeft(
            ['customer_group_ticket' => $groupSubQuery],
            'main_table.' . TicketInterface::ENTITY_ID . ' = customer_group_ticket.entity_id',
            ['customer_group_id' => 'customer_group_ticket.group_id']
        );
    }
}
