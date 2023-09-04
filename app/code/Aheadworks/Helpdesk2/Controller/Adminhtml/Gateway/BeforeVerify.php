<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

/**
 * Before verify action
 */
class BeforeVerify extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';
    const GATEWAY_DATA = 'aw_helpdesk2_gateway_data';

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $sessionManager;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @param Context $context
     * @param SessionManagerInterface $sessionManager
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $sessionManager,
        JsonFactory $resultJsonFactory
    ) {
        $this->sessionManager = $sessionManager;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $gatewayParams = $this->getRequest()->getParams();
        if ($gatewayParams) {
            $this->sessionManager->setData(self::GATEWAY_DATA, $gatewayParams);
        }

        $result = [
            'error'     => false,
            'message'   => __('Success')
        ];

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
