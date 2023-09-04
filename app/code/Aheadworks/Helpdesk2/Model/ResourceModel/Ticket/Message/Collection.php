<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message;

use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message as MessageModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ProcessorInterface $objectDataProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        private readonly ProcessorInterface $objectDataProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(MessageModel::class, MessageResourceModel::class);
    }

    /**
     * Add ticket filter
     *
     * @param int $ticketId
     * @return $this
     */
    public function addTicketFilter($ticketId)
    {
        $this->addFieldToFilter(MessageInterface::TICKET_ID, $ticketId);
        return $this;
    }

    /**
     * Add discussion type filter
     *
     * @return $this
     */
    public function addDiscussionTypeFilter()
    {
        return $this->addFieldToFilter(
            MessageInterface::TYPE,
            [
                'in' => [
                    MessageType::CUSTOMER,
                    MessageType::ADMIN,
                    MessageType::ESCALATION
                ]
            ]
        );
    }

    /**
     * Add message type filter
     *
     * @param string $type
     * @return $this
     */
    public function addMessageTypeFilter($type)
    {
        $this->addFieldToFilter(MessageInterface::TYPE, $type);
        return $this;
    }

    /**
     * Sort by created at desc
     *
     * @param string $direction
     * @return $this
     */
    public function sortByCreatedAt($direction = SortOrder::SORT_DESC)
    {
        return $this->addOrder(MessageInterface::CREATED_AT, $direction);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            MessageResourceModel::ATTACHMENT_TABLE_NAME,
            MessageInterface::ID,
            MessageAttachmentInterface::MESSAGE_ID,
            [
                MessageAttachmentInterface::ID,
                MessageAttachmentInterface::MESSAGE_ID,
                MessageAttachmentInterface::FILE_PATH,
                MessageAttachmentInterface::FILE_NAME,
            ],
            MessageInterface::ATTACHMENTS,
            [],
            [],
            true
        );

        /** @var MessageInterface $model */
        foreach ($this as $model) {
            $this->objectDataProcessor->prepareModelAfterLoad($model);
        }

        return parent::_afterLoad();
    }
}
