<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\MergeTicket;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json as ResultJson;

/**
 * Class TicketInfo
 */
class TicketInfo extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * TicketInfo constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CommandInterface $prepareInfoCommand
     */
    public function __construct(
        Context $context,
        private JsonFactory $resultJsonFactory,
        private CommandInterface $prepareInfoCommand
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
            $result = $this->prepareInfoCommand->execute($requestParams);

            return $resultJson->setData(['data' => $result, 'success' => true]);
        } catch (\Exception $exception) {
            return $resultJson->setData(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
