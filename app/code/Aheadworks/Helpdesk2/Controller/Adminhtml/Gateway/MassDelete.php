<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection;

/**
 * Class MassDelete
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    protected function massAction(Collection $collection)
    {
        $deletedRecords = 0;
        foreach ($collection->getAllIds() as $departmentId) {
            $this->gatewayRepository->deleteById($departmentId);
            $deletedRecords++;
        }

        if ($deletedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $deletedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been deleted.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
