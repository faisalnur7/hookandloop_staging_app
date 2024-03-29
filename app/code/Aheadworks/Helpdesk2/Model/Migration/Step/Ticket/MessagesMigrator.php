<?php
namespace Aheadworks\Helpdesk2\Model\Migration\Step\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Attachment\Factory as AttachmentFactory;
use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;

/**
 * Class MessagesReader
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step\Ticket
 */
class MessagesMigrator
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $messageTypeMap = [
        'admin-reply' => MessageType::ADMIN,
        'admin-internal' => MessageType::INTERNAL,
        'customer-reply' => MessageType::CUSTOMER,
        'system-message' => MessageType::SYSTEM
    ];

    /**
     * MessagesMigrator constructor.
     * @param ResourceConnection $resource
     * @param AttachmentFactory $attachmentFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageInterfaceFactory $messageFactory
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ResourceConnection $resource,
        AttachmentFactory $attachmentFactory,
        DataObjectHelper $dataObjectHelper,
        MessageRepositoryInterface $messageRepository,
        MessageInterfaceFactory $messageFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->resourceConnection = $resource;
        $this->attachmentFactory = $attachmentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * Migrate messages
     *
     * @param int $hdu1TicketId
     * @param int $hdu2TicketId
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function migrateMessages($hdu1TicketId, $hdu2TicketId)
    {
        $messagesToMigrate = $this->getMessagesToMigrate($hdu1TicketId);
        foreach ($messagesToMigrate as &$messageData) {
            $messageData[MessageInterface::TICKET_ID] = $hdu2TicketId;
            $messageData[MessageInterface::TYPE] = $this->messageTypeMap[$messageData[MessageInterface::TYPE]];
            $messageData[MessageInterface::CONTENT] = $messageData[MessageInterface::CONTENT] ?? '';
            unset($messageData['author_email']);
        }
        $this->saveToDataBase(MessageResourceModel::MAIN_TABLE_NAME, $messagesToMigrate);

        $attachments = $this->getMessageAttachmentData($hdu1TicketId);
        $this->saveAttachments($attachments);
    }

    /**
     * Get messages data
     *
     * @param int $hdu1TicketId
     * @return array[]
     */
    private function getMessagesToMigrate($hdu1TicketId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from($connection->getTableName(Helpdesk1TableNames::TICKET_MESSAGE))
            ->where('ticket_id = ?', $hdu1TicketId)
            ->columns([
                MessageInterface::TYPE => 'type',
                MessageInterface::CONTENT => 'content',
                MessageInterface::AUTHOR_NAME => 'author_name',
                MessageInterface::CREATED_AT => 'created_at',
            ]);

        return $connection->fetchAll($select);
    }

    /**
     * Get message attachment data
     *
     * @param int $hdu1TicketId
     * @return array
     */
    private function getMessageAttachmentData($hdu1TicketId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(['main_table' => $connection->getTableName(Helpdesk1TableNames::TICKET_MESSAGE)], [])
            ->joinInner(
                ['attach_tbl' => $connection->getTableName(Helpdesk1TableNames::ATTACHMENT)],
                'main_table.id = attach_tbl.message_id',
                [
                    MessageAttachmentInterface::ID => 'attach_tbl.id',
                    MessageAttachmentInterface::MESSAGE_ID => 'attach_tbl.message_id',
                    'filename' => 'attach_tbl.name',
                    'content' => 'attach_tbl.content'
                ]
            )->where('ticket_id = ?', $hdu1TicketId);

        return $connection->fetchAll($select);
    }

    /**
     * Save attachments data
     *
     * @param array $messageAttachmentData
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function saveAttachments($messageAttachmentData)
    {
        foreach ($messageAttachmentData as $index => &$attachment) {
            $info = pathinfo($attachment['filename']);
            $ext = $info['extension'] ?? '.';
            if (empty($info['extension'])) {
                $attachment['filename'] .= $ext;
            }
            if (!$this->getNotProtectedExtension()->isValid($ext)) {
                unset($messageAttachmentData[$index]);
                continue;
            }
            $attachmentObject = $this->attachmentFactory->create($attachment);
            $attachment[MessageAttachmentInterface::FILE_PATH] = $attachmentObject->getFilePath();
            $attachment[MessageAttachmentInterface::FILE_NAME] = $attachmentObject->getFileName();
            unset($attachment['filename']);
            unset($attachment['content']);
        }

        if (!empty($messageAttachmentData)) {
            $this->saveToDataBase(MessageResourceModel::ATTACHMENT_TABLE_NAME, $messageAttachmentData);
        }
    }

    /**
     * Save data to database
     *
     * @param string $table
     * @param array $data
     */
    private function saveToDataBase($table, $data)
    {
        $this->resourceConnection
            ->getConnection()
            ->insertOnDuplicate($this->resourceConnection->getTableName($table), $data);
    }

    /**
     * @return NotProtectedExtension|mixed
     */
    private function getNotProtectedExtension()
    {
        return $this->objectManager->get(NotProtectedExtension::class);
    }
}
