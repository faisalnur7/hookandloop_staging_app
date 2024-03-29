<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\Source\RejectedMessage\Type as RejecteMessageType;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage\Collection;

/**
 * Class MassMarkAsUnprocessed
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message
 */
class MassMarkAsUnprocessed extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function massAction(Collection $collection)
    {
        $collection
            ->addFieldToFilter(RejectedMessageInterface::TYPE, RejecteMessageType::EMAIL);

        $updatedRecords = 0;
        foreach ($collection->getItems() as $item) {
            if ($this->massActionCommand->execute(['item' => $item])) {
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
