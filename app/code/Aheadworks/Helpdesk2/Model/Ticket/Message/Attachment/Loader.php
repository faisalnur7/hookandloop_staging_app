<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Message\Attachment;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;

/**
 * Class Loader
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Message\Attachment
 */
class Loader
{
    /**
     * @var MessageResourceModel
     */
    private $messageResource;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var MessageAttachmentInterfaceFactory
     */
    private $attachmentFactory;

    /**
     * @param MessageResourceModel $messageResource
     * @param DataObjectHelper $dataObjectHelper
     * @param MessageAttachmentInterfaceFactory $attachmentFactory
     */
    public function __construct(
        MessageResourceModel $messageResource,
        DataObjectHelper $dataObjectHelper,
        MessageAttachmentInterfaceFactory $attachmentFactory
    ) {
        $this->messageResource = $messageResource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->attachmentFactory = $attachmentFactory;
    }

    /**
     * Load by attachment ID
     *
     * @param int $attachmentId
     * @return MessageAttachmentInterface
     * @throws NoSuchEntityException
     */
    public function loadById($attachmentId)
    {
        $attachmentData = $this->messageResource->loadAttachment($attachmentId);
        if (!$attachmentData) {
            throw NoSuchEntityException::singleField(MessageAttachmentInterface::ID, $attachmentId);
        }
        /** @var MessageAttachmentInterface $attachment */
        $attachment = $this->attachmentFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $attachment,
            $attachmentData,
            MessageAttachmentInterface::class
        );

        return $attachment;
    }
}
