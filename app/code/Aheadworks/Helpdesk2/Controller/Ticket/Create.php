<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page as ResultPage;

/**
 * Class Create
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
class Create extends CustomerAbstractAction
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Create New Ticket'));

        return $resultPage;
    }
}
