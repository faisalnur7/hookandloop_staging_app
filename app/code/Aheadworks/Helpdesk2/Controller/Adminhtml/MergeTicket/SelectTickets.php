<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\MergeTicket;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;

/**
 * Class SelectTickets
 */
class SelectTickets extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * SelectTickets constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CommandInterface $selectCommand
     */
    public function __construct(
        Context $context,
        private JsonFactory $resultJsonFactory,
        private CommandInterface $selectCommand
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

            $result = $this->selectCommand->execute($requestParams);

            if (!$result) {
                throw new LocalizedException(__('No matching tickets found. Please select others.'));
            }

            return $resultJson->setData(['data' => $result, 'success' => true]);
        } catch (\Exception $exception) {
            return $resultJson->setData(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
