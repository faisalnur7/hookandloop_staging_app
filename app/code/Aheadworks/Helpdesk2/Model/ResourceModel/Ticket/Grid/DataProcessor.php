<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class DataProcessor
 */
class DataProcessor
{
    const DEFAULT_MESSAGE = "\n\r";

    /**
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param TicketRepositoryInterface $ticketRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly MessageCollectionFactory $messageCollectionFactory,
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly EncryptorInterface $encryptor
    ) {}

    /**
     * Prepare aggregated data by entity
     *
     * @param TicketInterface $entity
     * @return array
     */
    public function prepareAggregatedDataByEntity($entity)
    {
        /** @var MessageCollection $messageCollection */
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addTicketFilter($entity->getEntityId());
        $messageCollection->addDiscussionTypeFilter();
        $messageCollection->sortByCreatedAt(SortOrder::SORT_DESC);

        /** @var MessageInterface $lastMessage */
        $lastMessage = $messageCollection->getFirstItem();
        /** @var MessageInterface $firstMessage */
        $firstMessage = $messageCollection->getLastItem();

        $orderId = $entity->getOrderId();
        $orderIncrementId = null;
        if ($orderId) {
            $orderIncrementId = $this->getOrderIncrementId($orderId);
        }

        return [
            GridInterface::ENTITY_ID => $entity->getEntityId(),
            GridInterface::UID => $entity->getUid(),
            GridInterface::RATING => $entity->getRating(),
            GridInterface::LAST_MESSAGE_DATE => $lastMessage->getCreatedAt(),
            GridInterface::LAST_MESSAGE_BY => $lastMessage->getAuthorName(),
            GridInterface::LAST_MESSAGE_TYPE => $lastMessage->getType(),
            GridInterface::DEPARTMENT_ID => $entity->getDepartmentId(),
            GridInterface::AGENT_ID => $entity->getAgentId(),
            GridInterface::SUBJECT => $entity->getSubject(),
            GridInterface::FIRST_MESSAGE_CONTENT => $this->encryptor->encrypt($firstMessage->getContent() ?? self::DEFAULT_MESSAGE),
            GridInterface::LAST_MESSAGE_CONTENT => $this->encryptor->encrypt($lastMessage->getContent() ?? self::DEFAULT_MESSAGE),
            GridInterface::IS_ENCRYPTED => true,
            GridInterface::CUSTOMER_NAME => $entity->getCustomerName(),
            GridInterface::CUSTOMER_EMAIL => $entity->getCustomerEmail(),
            GridInterface::CUSTOMER_ID => $entity->getCustomerId(),
            GridInterface::PRIORITY_ID => $entity->getPriorityId(),
            GridInterface::ORDER_ID => $orderId,
            GridInterface::ORDER_INCREMENT_ID => $orderIncrementId,
            GridInterface::CUSTOMER_MESSAGE_COUNT =>
                $this->getCustomerMessageCollection($entity->getEntityId())->getSize(),
            GridInterface::AGENT_MESSAGE_COUNT =>
                $this->getAgentMessageCollection($entity->getEntityId())->getSize(),
            GridInterface::MESSAGE_COUNT => $messageCollection->getSize(),
            GridInterface::STATUS_ID => $entity->getStatusId()
        ];
    }

    /**
     * Prepare aggregated data by entity ID
     *
     * @param int $entityId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareAggregatedDataByEntityId($entityId)
    {
        $entity = $this->ticketRepository->getById($entityId);
        return $this->prepareAggregatedDataByEntity($entity);
    }

    /**
     * Get customer message collection
     *
     * @param int $ticketId
     * @return MessageCollection
     */
    private function getCustomerMessageCollection($ticketId)
    {
        /** @var MessageCollection $customerMessageCollection */
        $customerMessageCollection = $this->messageCollectionFactory->create();
        $customerMessageCollection->addTicketFilter($ticketId);
        $customerMessageCollection->addMessageTypeFilter(MessageType::CUSTOMER);

        return $customerMessageCollection;
    }

    /**
     * Get agent message collection
     *
     * @param int $ticketId
     * @return MessageCollection
     */
    private function getAgentMessageCollection($ticketId)
    {
        /** @var MessageCollection $agentMessageCollection */
        $agentMessageCollection = $this->messageCollectionFactory->create();
        $agentMessageCollection->addTicketFilter($ticketId);
        $agentMessageCollection->addMessageTypeFilter(MessageType::ADMIN);

        return $agentMessageCollection;
    }

    /**
     * Get order increment id
     *
     * @param int $orderId
     * @return string|null
     */
    private function getOrderIncrementId($orderId)
    {
        return $this->orderRepository->get($orderId)->getIncrementId();
    }
}
