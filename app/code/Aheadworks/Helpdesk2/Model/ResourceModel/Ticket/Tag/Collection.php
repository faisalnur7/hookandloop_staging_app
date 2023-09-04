<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag;

use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Tag;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as ResourceTag;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Tag::class, ResourceTag::class);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray(TagInterface::ID, TagInterface::NAME);
    }
}
