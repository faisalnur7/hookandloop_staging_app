<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\QuickResponse;

use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Forward as ResultForward;

/**
 * Class NewAction
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\QuickResponse
 */
class NewAction extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::quick_responses';

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Forward to edit
     *
     * @return ResultForward
     */
    public function execute()
    {
        /** @var ResultForward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
