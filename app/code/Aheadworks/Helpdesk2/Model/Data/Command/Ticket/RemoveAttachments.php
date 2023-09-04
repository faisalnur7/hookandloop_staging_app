<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResource;
use Aheadworks\Helpdesk2\Model\FileSystem\Writer as FileSystemWriter;
use Aheadworks\Helpdesk2\Model\FileSystem\FileInfo;

/**
 * Class RemoveAttachments
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class RemoveAttachments implements CommandInterface
{
    /**
     * @var TicketResource
     */
    private $ticketResource;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @param TicketResource $ticketResource
     * @param FileSystemWriter $fileSystemWriter
     */
    public function __construct(
        TicketResource $ticketResource,
        FileSystemWriter $fileSystemWriter
    ) {
        $this->ticketResource = $ticketResource;
        $this->fileSystemWriter = $fileSystemWriter;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[TicketInterface::ENTITY_ID])) {
            throw new \InvalidArgumentException(
                'Ticket entity ID param is required to remove attachments'
            );
        }

        $attachments = $this->ticketResource->getTicketAttachments($data[TicketInterface::ENTITY_ID]);
        foreach ($attachments as $attachment) {
            $this->fileSystemWriter->removeFileFromMedia(
                FileInfo::FILE_DIR,
                $attachment[MessageAttachmentInterface::FILE_PATH]
            );
        }

        return true;
    }
}
