<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Message;

use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Delete\Content as ContentDeleteMessage;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type;
use Aheadworks\Helpdesk2\Model\FileSystem\Writer as FileSystemWriter;
use Aheadworks\Helpdesk2\Model\FileSystem\FileInfo;

class Delete implements CommandInterface
{
    /**
     * @param MessageRepositoryInterface $messageRepository
     * @param TicketManagementInterface $ticketManagement
     * @param ContentDeleteMessage $contentDeleteMessage
     * @param FileSystemWriter $fileSystemWriter
     */
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private TicketManagementInterface $ticketManagement,
        private ContentDeleteMessage $contentDeleteMessage,
        private FileSystemWriter $fileSystemWriter
    ) {
    }

    /**
     * Execute command
     *
     * @param array $messageData
     * @return MessageInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($messageData): MessageInterface
    {
        $message = $this->messageRepository->getById(
            $messageData[MessageInterface::ID]
        );

        foreach ($message->getAttachments() as $attachment) {
            $this->fileSystemWriter->removeFileFromMedia(
                FileInfo::FILE_DIR,
                $attachment[MessageAttachmentInterface::FILE_PATH]
            );
        }
        $this->prepareMessage($message);

        return $this->messageRepository->save($message);
    }

    /**
     * Prepare deletion message
     *
     * @param MessageInterface $message
     * @return MessageInterface
     */
    private function prepareMessage(MessageInterface $message): MessageInterface
    {
        $message->setAttachments([]);
        $message->setType(Type::SYSTEM);
        $message->setContent($this->contentDeleteMessage->getContent());

        return $message;
    }
}
