<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface as TicketGridInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\DataProcessor;

class Grid extends AbstractDb
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_ticket_grid';

    /**
     * @param Context $context
     * @param DataProcessor $dataProcessor
     * @param string|null $connectionName
     */
    public function __construct(
        private DataProcessor $dataProcessor,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TicketGridInterface::ENTITY_ID);
    }

    /**
     * Refresh data in ticket grid
     *
     * @param int|TicketInterface $entity
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function refresh($entity)
    {
        $data = $entity instanceof TicketInterface
            ? $this->dataProcessor->prepareAggregatedDataByEntity($entity)
            : $this->dataProcessor->prepareAggregatedDataByEntityId($entity);

        if (!empty($data[TicketGridInterface::LAST_MESSAGE_DATE])
            && !empty($data[TicketGridInterface::LAST_MESSAGE_BY])
            && !empty($data[TicketGridInterface::LAST_MESSAGE_TYPE])
        ) {
            $this->getConnection()
                ->insertOnDuplicate($this->getTable(self::MAIN_TABLE_NAME), $data);
        }
    }
}
