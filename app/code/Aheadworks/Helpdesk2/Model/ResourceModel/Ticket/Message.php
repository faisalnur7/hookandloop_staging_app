<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Message
 */
class Message extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_ticket_message';
    const ATTACHMENT_TABLE_NAME = 'aw_helpdesk2_ticket_message_attachment';

    /**#@-
     * @param Context $context
     * @param EntityManager $entityManager
     * @param EncryptorInterface $encryptor
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        private readonly EncryptorInterface $encryptor,
        $connectionName = null
    ) {
        parent::__construct($context, $entityManager, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, MessageInterface::ID);
    }

    /**
     * Get ticket ID by attachment ID
     *
     * @param int $attachmentId
     * @return int|bool
     * @throws LocalizedException
     */
    public function getTicketIdByAttachmentId($attachmentId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['main_table' => $this->getMainTable()], MessageInterface::TICKET_ID)
            ->joinLeft(
                ['attach_tbl' => $this->getTable(self::ATTACHMENT_TABLE_NAME)],
                'main_table.id = attach_tbl.message_id',
                []
            )
            ->where('attach_tbl.id = ?', $attachmentId);

        $result = $connection->fetchCol($select);
        return reset($result);
    }

    /**
     * Load attachment
     *
     * @param int $attachmentId
     * @return array|bool
     */
    public function loadAttachment($attachmentId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::ATTACHMENT_TABLE_NAME))
            ->where('id = ?', $attachmentId);

        return $connection->fetchRow($select);
    }

    /**
     * Check if content is encrypted
     *
     * @param DataObject $message
     * @return Message
     */
    protected function _afterSave(DataObject $message)
    {
        if ($message->getContent() && $message->getIsEncrypted()) {
            $content = $message->getContent();
            $message->setContent($this->encryptor->decrypt($content));
        }

        return parent::_afterSave($message);
    }
}
