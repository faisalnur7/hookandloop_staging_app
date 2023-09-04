<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Builder as MetadataBuilder;
use Aheadworks\Helpdesk2\Model\Email\Sender as EmailSender;
use Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail\CanSendEmailCheckerInterface;
use Aheadworks\Helpdesk2\Model\Config;

/**
 * Class SendEmailToSupervisor
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class SendEmailToSupervisor implements ActionInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MetadataBuilder
     */
    private $metadataBuilder;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var CanSendEmailCheckerInterface
     */
    private $canSendEmailChecker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param MetadataBuilder $metadataBuilder
     * @param EmailSender $emailSender
     * @param CanSendEmailCheckerInterface $canSendEmailChecker
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        MetadataBuilder $metadataBuilder,
        EmailSender $emailSender,
        CanSendEmailCheckerInterface $canSendEmailChecker,
        Config $config
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->metadataBuilder = $metadataBuilder;
        $this->emailSender = $emailSender;
        $this->canSendEmailChecker = $canSendEmailChecker;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function run($actionData, $eventData)
    {
        if (!$this->canSendEmailChecker->canSend($actionData, $eventData)) {
            return false;
        }
        $emailMetadata = $this->metadataBuilder->buildForAction($actionData['action'], $eventData);
        $storeId = $this->storeManager->getStore()->getId();
        $emailMetadata->setTemplateId($this->config->getEscalationEmailTemplate($storeId));
        $supervisorEmails = $this->config->getEscalationSupervisorEmails($storeId);
        try {
            foreach ($supervisorEmails as $email) {
                $emailMetadata->setRecipientEmail($email);
                $this->emailSender->send($emailMetadata);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }

        return true;
    }
}
