<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Management as MessageManagement;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Builder as MetadataBuilder;
use Aheadworks\Helpdesk2\Model\Email\Sender as EmailSender;
use Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail\CanSendEmailCheckerInterface;

/**
 * Class SendEmail
 */
class SendEmail implements ActionInterface
{
    /**
     * @param LoggerInterface $logger
     * @param MetadataBuilder $metadataBuilder
     * @param EmailSender $emailSender
     * @param CanSendEmailCheckerInterface $canSendEmailChecker
     * @param MessageManagement $messageManagement
     * @param array $messageContent
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MetadataBuilder $metadataBuilder,
        private readonly EmailSender $emailSender,
        private readonly CanSendEmailCheckerInterface $canSendEmailChecker,
        private readonly MessageManagement $messageManagement,
        private readonly array $messageContent = []
    ) {}

    /**
     * Run action
     *
     * @param array $actionData
     * @param EventDataInterface $eventData
     * @throws LocalizedException
     * @return bool
     */
    public function run($actionData, $eventData)
    {
        if (!$this->canSendEmailChecker->canSend($actionData, $eventData)) {
            return false;
        }
        $emailMetadata = $this->metadataBuilder->buildForAction($actionData['action'], $eventData);
        $emailMetadata->setTemplateId($actionData['value']);

        try {
            $this->emailSender->send($emailMetadata);

            $this->messageManagement->createAutomationMessage(
                $eventData->getTicket()->getEntityId(),
                $this->messageContent['action'] ?? '',
                $this->messageContent['to'] ?? '',
                $eventData->getEventName()
            );
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }

        return true;
    }
}
