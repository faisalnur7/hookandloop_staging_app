<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection;

/**
 * Class MassChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class MassChangeStatus extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function massAction(Collection $collection)
    {
        $isActive = (bool)$this->getRequest()->getParam(GatewayDataInterface::IS_ACTIVE);
        $updatedRecords = 0;

        foreach ($collection->getAllIds() as $gatewayId) {
            $gateway = $this->gatewayRepository->get($gatewayId);
            if ($gateway->getIsActive() != $isActive) {
                $gateway->setIsActive($isActive);
                $this->gatewayRepository->save($gateway);
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
