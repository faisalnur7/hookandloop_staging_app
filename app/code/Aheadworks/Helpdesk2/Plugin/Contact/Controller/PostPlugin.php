<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Plugin\Contact\Controller;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\RejectedMessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Config;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Department\GuestChecker;
use Aheadworks\Helpdesk2\Model\Rejection\Validator;
use Aheadworks\Helpdesk2\Model\Rejection\Validator\DataBuilder\ContactUsForm;
use Aheadworks\Helpdesk2\Model\Source\RejectedMessage\Type as RejectedMessageType;
use Magento\Contact\Controller\Index\Post as PostController;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class PostPlugin
 */
class PostPlugin
{
    /**
     * @param Config $config
     * @param CommandInterface $createCommand
     * @param ProcessorInterface $postDataProcessor
     * @param Validator $rejectingPatternsValidator
     * @param ContactUsForm $validationDataBuilder
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param GuestChecker $guestChecker
     * @param RejectedMessageInterfaceFactory $rejectedMessageFactory
     * @param RejectedMessageRepositoryInterface $rejectedMessageRepository
     */
    public function __construct(
        private readonly Config $config,
        private readonly CommandInterface $createCommand,
        private readonly ProcessorInterface $postDataProcessor,
        private readonly Validator $rejectingPatternsValidator,
        private readonly ContactUsForm $validationDataBuilder,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectFactory $resultRedirectFactory,
        private readonly GuestChecker $guestChecker,
        private readonly RejectedMessageInterfaceFactory $rejectedMessageFactory,
        private readonly RejectedMessageRepositoryInterface $rejectedMessageRepository
    ) {}

    /**
     * Rewrite native contact us controller and create ticket via contact us form
     *
     * @param PostController $subject
     * @param callable $proceed
     * @return ResultRedirect
     */
    public function aroundExecute(PostController $subject, callable $proceed)
    {
        if (!$this->config->isEnabledContactFormIntegration()) {
            return $proceed();
        }

        try {
            $ticketData = $this->prepareTicketData($subject->getRequest());
            if (!$this->guestChecker->canBeUsedByGuest($ticketData[TicketInterface::DEPARTMENT_ID])) {
                throw new LocalizedException(__('Please, log in to submit this request type'));
            }
            $validationData = $this->validationDataBuilder->build($ticketData);
            $validationResult = $this->rejectingPatternsValidator->validate($validationData);
            if (!$validationResult->isRejected()) {
                $this->createCommand->execute($ticketData);
            } else {
                $this->rejectMessage($ticketData, $validationResult->getPatternId());
            }
            $this->messageManager->addSuccess(__('Ticket was successfully created'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while creating the ticket'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * Prepare ticket data
     *
     * @param RequestInterface $request
     * @return array
     */
    private function prepareTicketData($request)
    {
        $params = $request->getParams();
        $ticketData = [
            TicketInterface::CUSTOMER_EMAIL => $params['email'],
            TicketInterface::CUSTOMER_NAME => $params['name'],
            MessageInterface::CONTENT => $params['comment'],
            TicketInterface::SUBJECT => $params['subject'],
            TicketInterface::TELEPHONE => $params['telephone'],
            TicketInterface::ORDER_ID => $params[TicketInterface::ORDER_ID] ?? null,
            TicketInterface::DEPARTMENT_ID => $params[TicketInterface::DEPARTMENT_ID] ?? null,
            TicketInterface::OPTIONS => $params[TicketInterface::OPTIONS] ?? null,
            TicketInterface::CC_RECIPIENTS => $params[TicketInterface::CC_RECIPIENTS]
                ? $params[TicketInterface::CC_RECIPIENTS] : null,
        ];

        return $this->postDataProcessor->prepareEntityData($ticketData);
    }

    /**
     * Reject message
     *
     * @param array $ticketData
     * @param int $rejectionPatternId
     * @return RejectedMessageInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function rejectMessage($ticketData, $rejectionPatternId)
    {
        /** @var RejectedMessageInterface $rejectedMessage */
        $rejectedMessage = $this->rejectedMessageFactory->create();

        $rejectedMessage
            ->setType(RejectedMessageType::CONTACT_US_FORM)
            ->setFrom($ticketData[TicketInterface::CUSTOMER_EMAIL])
            ->setSubject($ticketData[TicketInterface::SUBJECT])
            ->setContent($ticketData[MessageInterface::CONTENT])
            ->setRejectPatternId($rejectionPatternId)
            ->setMessageData($ticketData);

        return $this->rejectedMessageRepository->save($rejectedMessage);
    }
}
