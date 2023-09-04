<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\FormDataProvider as TicketFormDataProvider;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
class Save extends CustomerAbstractAction
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param Session $customerSession
     * @param DataPersistorInterface $dataPersistor
     * @param CommandInterface $saveCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        Session $customerSession,
        DataPersistorInterface $dataPersistor,
        CommandInterface $saveCommand,
        ProcessorInterface $postDataProcessor
    ) {
        parent::__construct($context, $ticketRepository, $customerSession);
        $this->dataPersistor = $dataPersistor;
        $this->saveCommand = $saveCommand;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $ticketData = $this->postDataProcessor->prepareEntityData($data);
                /** @var TicketInterface $ticket */
                $this->saveCommand->execute($ticketData);
                $this->dataPersistor->clear(TicketFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Ticket was successfully created'));

                return $resultRedirect->setPath('aw_helpdesk2/ticket');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while creating the ticket'));
            }

            $this->dataPersistor->set(TicketFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
        }

        return $resultRedirect->setPath('*/*/create');
    }
}
