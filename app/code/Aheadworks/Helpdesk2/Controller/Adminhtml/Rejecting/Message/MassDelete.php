<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage\Collection;

/**
 * Class MassDelete
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message
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

        foreach ($collection->getItems() as $item) {
            $this->massActionCommand->execute(['item' => $item]);
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
