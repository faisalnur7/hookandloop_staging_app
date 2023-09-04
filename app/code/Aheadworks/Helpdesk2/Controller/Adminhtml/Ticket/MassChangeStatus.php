<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Collection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\LocalizedException;

class MassChangeStatus extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function massAction(Collection $collection)
    {
        $request = $this->getRequest();
        $data[TicketInterface::STATUS_ID] = $request->getParam(TicketInterface::STATUS_ID);
        $data['selected'] = $collection->getAllIds();
        try {
            $this->massActionCommand->execute($data);
            $this->messageManager->addSuccessMessage(__('Statuses have been changed.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($request->getServer('HTTP_REFERER')) {
            $resultRedirect->setUrl($request->getServer('HTTP_REFERER'));
        } else {
            $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}
