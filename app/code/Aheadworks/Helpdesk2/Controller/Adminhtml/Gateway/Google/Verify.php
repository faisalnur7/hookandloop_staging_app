<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway\Google;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class Verify
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway\Google
 */
class Verify extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var array
     */
    protected $_publicActions = ['verify'];

    /**
     * @var CommandInterface
     */
    private $verifyGoogleAccountCommand;

    /**
     * @param Context $context
     * @param CommandInterface $verifyGoogleAccountCommand
     */
    public function __construct(
        Context $context,
        CommandInterface $verifyGoogleAccountCommand
    ) {
        $this->verifyGoogleAccountCommand = $verifyGoogleAccountCommand;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $this->verifyGoogleAccountCommand->execute($params);
        return $this->resultFactory->create($this->resultFactory::TYPE_PAGE);
    }
}
