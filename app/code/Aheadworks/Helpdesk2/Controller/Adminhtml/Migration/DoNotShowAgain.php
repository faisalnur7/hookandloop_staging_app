<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Migration;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\FlagManager;

/**
 * Class DoNotShowAgain
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Migration
 */
class DoNotShowAgain extends Action
{
    const DO_NOT_SHOW_AGAIN_FLAG = 'aw_helpdesk2_migration_do_not_show_notification_again';

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param FlagManager $flagManager
     */
    public function __construct(
        Context $context,
        FlagManager $flagManager
    ) {
        parent::__construct($context);
        $this->flagManager = $flagManager;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isAjax() && $request->getParam('doNotShowAgain')) {
            $this->flagManager->saveFlag(self::DO_NOT_SHOW_AGAIN_FLAG, 1);
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(true);
    }
}
