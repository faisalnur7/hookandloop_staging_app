<?php
namespace Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread;

use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;

/**
 * Class DiscussionMessages
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread
 */
class DiscussionMessages implements ProviderInterface
{
    /**
     * @var MessageCollection
     */
    protected $collection;

    /**
     * @param MessageCollectionFactory $collectionFactory
     */
    public function __construct(MessageCollectionFactory $collectionFactory)
    {
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @param int $ticketId
     * @return array
     */
    public function getData($ticketId)
    {
        $this->collection
            ->addTicketFilter($ticketId)
            ->addDiscussionTypeFilter()
            ->sortByCreatedAt(SortOrder::SORT_DESC);

        return $this->collection->toArray();
    }
}
