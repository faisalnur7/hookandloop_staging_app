<?php
namespace Aheadworks\Helpdesk2\Model\Email;

use Aheadworks\Helpdesk2\Model\Email\Template\TransportBuilder;
use Aheadworks\Helpdesk2\Model\Email\Template\TransportBuilderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;

/**
 * Class Sender
 *
 * @package Aheadworks\Helpdesk2\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilderFactory
     */
    private $transportBuilderFactory;

    /**
     * @param TransportBuilderFactory $transportBuilderFactory
     */
    public function __construct(
        TransportBuilderFactory $transportBuilderFactory
    ) {
        $this->transportBuilderFactory = $transportBuilderFactory;
    }

    /**
     * Send email message
     *
     * @param MetadataInterface $emailMetadata
     * @throws LocalizedException
     * @throws MailException
     */
    public function send($emailMetadata)
    {
        /** @var TransportBuilder $transportBuilder */
        $transportBuilder = $this->transportBuilderFactory->create();

        $transportBuilder
            ->setTemplateModel(Template::class)
            ->setTemplateIdentifier($emailMetadata->getTemplateId())
            ->setTemplateOptions($emailMetadata->getTemplateOptions())
            ->setTemplateVars($emailMetadata->getTemplateVariables())
            ->setFromByScope(
                [
                    'name' => $emailMetadata->getSenderName(),
                    'email' => $emailMetadata->getSenderEmail()
                ]
            )->addTo(
                $emailMetadata->getRecipientEmail(),
                $emailMetadata->getRecipientName()
            );

        $attachments = $emailMetadata->getAttachments() ? : [];
        foreach ($attachments as $attachment) {
            $transportBuilder->addAttachment($attachment['content'], $attachment['name']);
        }
        if ($emailMetadata->getCcRecipients() && is_array($emailMetadata->getCcRecipients())) {
            foreach ($emailMetadata->getCcRecipients() as $ccRecipient) {
                $transportBuilder->addCc($ccRecipient);
            }
        }
        if ($emailMetadata->getEmailReplyTo()) {
            $transportBuilder->setReplyTo($emailMetadata->getEmailReplyTo());
        }
        if ($emailMetadata->getHeaders()) {
            $transportBuilder->addHeaders($emailMetadata->getHeaders());
        }

        $transportBuilder->getTransport()->sendMessage();
    }
}
