<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket;

use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Controller\Adminhtml\ActionWithJsonResponse;
use Aheadworks\Helpdesk2\Model\Result\JsonDataFactory as JsonDataFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;

class DeleteMessage extends ActionWithJsonResponse
{
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * @param PermissionManager $permissionManager
     * @param CommandInterface $deleteCommand
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param JsonDataFactory $jsonDataFactory
     */
    public function __construct(
        private PermissionManager $permissionManager,
        private CommandInterface $deleteCommand,
        Context $context,
        JsonFactory $resultJsonFactory,
        JsonDataFactory $jsonDataFactory
    ) {
        parent::__construct($context, $resultJsonFactory, $jsonDataFactory);
    }

    /**
     * Execute delete message action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                if (isset($data[MessageInterface::TICKET_ID]) &&
                    !$this->permissionManager->isAdminAbleToPerformTicketAction(
                        $data[MessageInterface::TICKET_ID],
                        DepartmentPermissionInterface::TYPE_UPDATE
                    )
                ) {
                    return $this->createErrorResponse(__('Not enough permissions to delete message.'));
                }
                $this->deleteCommand->execute($data);
                return $this->createSuccessResponse(__('Message was successfully deleted.'));
            } catch (LocalizedException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\RuntimeException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\Exception $e) {
                return $this->createErrorResponse(__('Something went wrong while deleting message.'));
            }
        }

        return $this->createErrorResponse(__('Something went wrong while deleting message.'));
    }
}
