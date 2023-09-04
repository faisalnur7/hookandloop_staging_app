<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;

/**
 * Class TicketPermission
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket
 */
class TicketPermission implements ProcessorInterface
{
    /**
     * @var PermissionManager
     */
    private $permissionManager;

    /**
     * @param PermissionManager $permissionManager
     */
    public function __construct(
        PermissionManager $permissionManager
    ) {
        $this->permissionManager = $permissionManager;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareEntityData($data)
    {
        $data['is_allowed_to_update_ticket'] = $this->permissionManager->isAdminAbleToPerformTicketAction(
            $this->getTicketId($data),
            DepartmentPermissionInterface::TYPE_UPDATE
        );

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }

    /**
     * Get ticket ID
     *
     * @param array $data
     * @return int
     * @throws LocalizedException
     */
    private function getTicketId($data)
    {
        if (isset($data[TicketInterface::ENTITY_ID])) {
            return $data[TicketInterface::ENTITY_ID];
        }

        if (isset($data['ticket_id'])) {
            return $data['ticket_id'];
        }

        throw new LocalizedException(__('Ticket ID is not specified'));
    }
}
