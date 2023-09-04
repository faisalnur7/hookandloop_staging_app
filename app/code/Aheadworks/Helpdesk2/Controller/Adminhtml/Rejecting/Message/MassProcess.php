<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage\Collection;

/**
 * Class MassProcess
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message
 */
class MassProcess extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function massAction(Collection $collection)
    {
        $processedRecords = 0;

        foreach ($collection->getItems() as $item) {
            if ($this->massActionCommand->execute(['item' => $item])) {
                $processedRecords++;
            }
        }

        if ($processedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been processed.', $processedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been processed.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
