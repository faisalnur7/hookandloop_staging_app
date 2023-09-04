<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Message;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;

/**
 * Class Info
 */
class Info
{
    /**
     * Info constructor.
     *
     * @param MessageCollectionFactory $messageCollectionFactory
     */
    public function __construct(private MessageCollectionFactory $messageCollectionFactory)
    {
    }

    /**
     * Retrieve ticket last message
     *
     * @param int $ticketId
     * @return MessageInterface
     */
    public function getLastMessage(int $ticketId): MessageInterface
    {
        /** @var MessageCollection $messageCollection */
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addTicketFilter($ticketId);
        $messageCollection->addDiscussionTypeFilter();
        $messageCollection->sortByCreatedAt(SortOrder::SORT_DESC);

        return $messageCollection->getFirstItem();
    }
}
