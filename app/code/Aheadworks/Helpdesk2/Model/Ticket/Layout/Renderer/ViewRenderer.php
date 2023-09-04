<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer;

use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class ViewRenderer
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer
 */
class ViewRenderer extends AbstractExtensibleObject implements ViewRendererInterface
{
    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getTicket()
    {
        return $this->_get(self::TICKET);
    }

    /**
     * @inheritdoc
     */
    public function setTicket($ticket)
    {
        return $this->setData(self::TICKET, $ticket);
    }
}
