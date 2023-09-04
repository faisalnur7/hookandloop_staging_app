<?php
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\CollectionFactory as TagCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Collection;

/**
 * Class Tags
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class Tags implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $tagCollection;

    /**
     * @var array
     */
    private $options;

    /**
     * @param TagCollectionFactory $tagCollectionFactory
     */
    public function __construct(TagCollectionFactory $tagCollectionFactory)
    {
        $this->tagCollection = $tagCollectionFactory->create();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->tagCollection->toOptionArray();
        }
        return $this->options;
    }
}
