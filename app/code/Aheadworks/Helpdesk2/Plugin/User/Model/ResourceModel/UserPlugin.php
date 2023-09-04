<?php
namespace Aheadworks\Helpdesk2\Plugin\User\Model\ResourceModel;

use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Magento\User\Model\ResourceModel\User as MagentoUserResourceModel;

/**
 * Class UserPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\User\Model\ResourceModel
 */
class UserPlugin
{
    /**
     * @var TicketResourceModel
     */
    private $ticketResourceModel;

    /**
     * @param TicketResourceModel $ticketResourceModel
     */
    public function __construct(TicketResourceModel $ticketResourceModel)
    {
        $this->ticketResourceModel = $ticketResourceModel;
    }

    /**
     * Update ticket tables after delete magento user
     *
     * @param MagentoUserResourceModel $subject
     * @param bool $result
     * @param \Magento\Framework\Model\AbstractModel $user
     * @return bool
     */
    public function afterDelete(MagentoUserResourceModel $subject, $result, $user)
    {
        if ($result) {
            $this->ticketResourceModel->resetAgentId($user->getId());
        }

        return $result;
    }
}
