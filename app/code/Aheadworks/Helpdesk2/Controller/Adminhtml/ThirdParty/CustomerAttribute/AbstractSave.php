<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\ThirdParty\CustomerAttribute;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Controller\Adminhtml\ActionWithJsonResponse;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Result\JsonDataFactory as JsonDataFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Magento\Framework\Controller\Result\Json;

/**
 * Class AbstractSave
 */
abstract class AbstractSave extends ActionWithJsonResponse
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magento_Customer::manage';

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param JsonDataFactory $jsonDataFactory
     * @param CommandInterface $saveCommand
     * @param PermissionManager $permissionManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        JsonDataFactory $jsonDataFactory,
        protected CommandInterface $saveCommand,
        protected PermissionManager $permissionManager
    ) {
        parent::__construct($context, $resultJsonFactory, $jsonDataFactory);
    }

    /**
     * Execute action based on request and return result
     *
     * @return Json
     */
    public function execute(): Json
    {
        $error = __('Something went wrong while saving customer attribute');
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                if (isset($data['ticket_id']) && isset($data[PermissionManager::TICKET_ACTION])
                    && !$this->permissionManager->isAdminAbleToPerformTicketAction(
                        $data['ticket_id'],
                        $data[PermissionManager::TICKET_ACTION]
                    )) {
                    return $this->createErrorResponse(__('Not enough permissions save customer attribute'));
                }
                $this->saveCommand->execute($data);
                return $this->createSuccessResponse(__('Customer attribute was successfully saved'));
            } catch (LocalizedException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\RuntimeException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\Exception $e) {
                return $this->createErrorResponse($error);
            }
        }

        return $this->createErrorResponse($error);
    }
}
