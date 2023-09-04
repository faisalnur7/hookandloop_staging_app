<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Department;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Collection;

/**
 * Class MassDelete
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Department
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function massAction(Collection $collection)
    {
        $deletedRecords = 0;
        foreach ($collection->getAllIds() as $departmentId) {
            $this->massActionCommand->execute([DepartmentInterface::ID => $departmentId]);
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
