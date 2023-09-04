<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Processor;

use Magento\User\Model\User as MagentoUser;
use Magento\User\Model\UserFactory as MagentoUserFactory;
use Magento\User\Model\ResourceModel\User as MagentoUserResourceModel;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Aheadworks\Helpdesk2\Model\Data\ContentModifier;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class MessageFactory
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Processor
 */
class MessageFactory
{
    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @var MagentoUserFactory
     */
    private $userFactory;

    /**
     * @var MagentoUserResourceModel
     */
    private $userResource;

    /**
     * @var EmailParser
     */
    private $emailParser;

    /**
     * @var MessageAttachmentInterfaceFactory
     */
    private $attachmentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ContentModifier
     */
    private $contentModifier;

    /**
     * MessageFactory constructor.
     * @param MessageInterfaceFactory $messageFactory
     * @param MagentoUserFactory $userFactory
     * @param MagentoUserResourceModel $userResource
     * @param EmailParser $emailParser
     * @param MessageAttachmentInterfaceFactory $attachmentFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        MessageInterfaceFactory $messageFactory,
        MagentoUserFactory $userFactory,
        MagentoUserResourceModel $userResource,
        EmailParser $emailParser,
        MessageAttachmentInterfaceFactory $attachmentFactory,
        DataObjectHelper $dataObjectHelper,
        ContentModifier $contentModifier
    ) {
        $this->messageFactory = $messageFactory;
        $this->userFactory = $userFactory;
        $this->userResource = $userResource;
        $this->emailParser = $emailParser;
        $this->attachmentFactory = $attachmentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->contentModifier = $contentModifier;
    }

    /**
     * Create new message based on ticket and mail
     *
     * @param TicketInterface $ticket
     * @param EmailInterface $mail
     * @return MessageInterface
     */
    public function create($ticket, $mail)
    {
        $messageType = MessageType::CUSTOMER;
        $authorName = $ticket->getCustomerName();
        $authorEmail = $ticket->getCustomerEmail();

        if ($ticket->getAgentId()) {
            /** @var MagentoUser $agent */
            $agent = $this->userFactory->create();
            $this->userResource->load($agent, $ticket->getAgentId());
            $agentEmail = $this->emailParser->parseFromSubject($mail->getFrom());
            if ($agent && $agent->getIsActive() && $agent->getEmail() == $agentEmail) {
                $messageType = MessageType::ADMIN;
                $authorName = $agent->getFirstName() . ' ' . $agent->getLastName();
                $authorEmail = $agent->getEmail();
            }
        }

        /** @var MessageInterface $message */
        $message = $this->messageFactory->create();
        $message
            ->setTicketId($ticket->getEntityId())
            ->setGatewayId($mail->getGatewayId())
            ->setContent($mail->getBody())
            ->setType($messageType)
            ->setAuthorName($authorName)
            ->setAuthorEmail($authorEmail)
            ->setCreatedAt($mail->getCreatedAt())
            ->setAttachments($this->getAttachments($mail));

        return $message;
    }

    /**
     * Get attachments
     *
     * @param EmailInterface $mail
     * @return MessageAttachmentInterface[]
     */
    private function getAttachments($mail)
    {
        $attachments = [];
        $emailAttachments = $mail->getAttachments();
        if (is_array($emailAttachments)) {
            foreach ($emailAttachments as $emailAttachment) {
                /** @var MessageAttachmentInterface $attachment */
                $attachment = $this->attachmentFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $attachment,
                    $emailAttachment,
                    MessageAttachmentInterface::class
                );

                $attachments[] = $attachment;
            }
        }

        return $attachments;
    }
}
