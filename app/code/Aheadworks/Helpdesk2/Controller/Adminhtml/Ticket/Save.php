<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\FormDataProvider as TicketFormDataProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CommandInterface $saveCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CommandInterface $saveCommand,
        ProcessorInterface $postDataProcessor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->createCommand = $saveCommand;
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
                $this->createCommand->execute($ticketData);
                $this->dataPersistor->clear(TicketFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Ticket was successfully saved'));

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the ticket'));
            }

            $this->dataPersistor->set(TicketFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
        }

        return $resultRedirect->setPath('*/*/create');
    }
}
