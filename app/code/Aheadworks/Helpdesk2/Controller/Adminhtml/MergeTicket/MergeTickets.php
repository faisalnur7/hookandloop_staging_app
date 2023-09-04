<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\MergeTicket;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;

/**
 * Class MergeTickets
 */
class MergeTickets extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * MergeTickets constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CommandInterface $mergeCommand
     */
    public function __construct(
        Context $context,
        private JsonFactory $resultJsonFactory,
        private CommandInterface $mergeCommand
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultJson
     */
    public function execute(): ResultJson
    {
        try {
            $resultJson = $this->resultJsonFactory->create();
            $requestParams = $this->getRequest()->getParams();

            $this->mergeCommand->execute($requestParams);

            $this->messageManager->addSuccessMessage(__('Tickets were successfully merge'));
            return $resultJson->setData(['success' => true]);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while merging the tickets: %1', $exception->getMessage())
            );
            return $resultJson->setData(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
