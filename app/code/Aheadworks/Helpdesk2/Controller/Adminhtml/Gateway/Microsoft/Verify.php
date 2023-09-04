<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway\Microsoft;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Verify action
 */
class Verify extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var array
     */
    protected $_publicActions = ['verify'];

    /**
     * @var CommandInterface
     */
    private CommandInterface $verifyMicrosoftAccountCommand;

    /**
     * @param Context $context
     * @param CommandInterface $verifyMicrosoftAccountCommand
     */
    public function __construct(
        Context $context,
        CommandInterface $verifyMicrosoftAccountCommand
    ) {
        $this->verifyMicrosoftAccountCommand = $verifyMicrosoftAccountCommand;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $params = $this->getRequest()->getParams();
        $this->verifyMicrosoftAccountCommand->execute($params);
        return $this->resultFactory->create($this->resultFactory::TYPE_PAGE);
    }
}
