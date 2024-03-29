<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Department;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Collection;

/**
 * Class MassChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Department
 */
class MassChangeStatus extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function massAction(Collection $collection)
    {
        $isActive = (bool)$this->getRequest()->getParam(GatewayDataInterface::IS_ACTIVE);
        $updatedRecords = 0;

        foreach ($collection->getAllIds() as $gatewayId) {
            $data = [
                GatewayDataInterface::IS_ACTIVE => $isActive,
                GatewayDataInterface::ID => $gatewayId
            ];
            if ($this->massActionCommand->execute($data)) {
                $updatedRecords++;
            }
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
