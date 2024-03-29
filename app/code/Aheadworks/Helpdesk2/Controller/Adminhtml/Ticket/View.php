<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Aheadworks\Helpdesk2\Model\Ticket\BackendUserHistory;

/**
 * Class View
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket
 */
class View extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var PermissionManager
     */
    private $permissionManager;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param PageFactory $resultPageFactory
     * @param PermissionManager $permissionManager
     * @param BackendUserHistory $backendUserHistory
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        PageFactory $resultPageFactory,
        PermissionManager $permissionManager,
        private BackendUserHistory $backendUserHistory
    ) {
        parent::__construct($context);
        $this->ticketRepository = $ticketRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->permissionManager = $permissionManager;
    }

    /**
     * View action
     *
     * @return ResultPage|ResultRedirect
     * @throws LocalizedException
     */
    public function execute()
    {
        try {
            $ticketId = (int)$this->getRequest()->getParam(TicketInterface::ENTITY_ID);
            $ticket = $this->ticketRepository->getById($ticketId);
            if (!$this->permissionManager->isAdminAbleToPerformTicketAction(
                $ticket->getEntityId(),
                DepartmentPermissionInterface::TYPE_VIEW
            )) {
                $this->messageManager->addErrorMessage(__('Not enough permissions to view the ticket'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }

            $pageTitle = $this->createPageTitle($ticket);

            /** @var ResultPage $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage
                ->setActiveMenu('Aheadworks_Helpdesk2::tickets')
                ->getConfig()->getTitle()->prepend($pageTitle);

            $this->backendUserHistory->addTicketIdToAdminHistory($ticketId);
            return $resultPage;
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('This ticket doesn\'t exist')
            );
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');

            return $resultRedirect;
        }
    }

    /**
     * Create page title
     *
     * @param TicketInterface $ticket
     * @return Phrase
     */
    private function createPageTitle($ticket)
    {
        return __(
            '[%1] %2: %3',
            [
                $ticket->getUid(),
                $ticket->getCustomerName(),
                $ticket->getSubject()
            ]
        );
    }
}
