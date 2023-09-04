<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Relation\StorefrontOption;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketOptionInterfaceFactory $ticketOptionFactory
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly MetadataPool $metadataPool,
        private readonly ResourceConnection $resourceConnection,
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly TicketOptionInterfaceFactory $ticketOptionFactory,
        private readonly EncryptorInterface $encryptor
    ) {}

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var TicketInterface $entity */
        if (!$entity->getEntityId()) {
            return $entity;
        }

        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TicketInterface::class)->getEntityConnectionName()
        );
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(TicketResourceModel::TICKET_OPTION_TABLE_NAME))
            ->where('ticket_id = :id');
        $optionRows = $connection->fetchAll($select, ['id' => $entity->getEntityId()]);

        $options = [];
        foreach ($optionRows as $optionRow) {
            /** @var TicketOptionInterface $option */
            $option = $this->ticketOptionFactory->create();
            $this->dataObjectHelper->populateWithArray($option, $optionRow, TicketOptionInterface::class);
            if ($option->getValue() && $option->getIsEncrypted()) {
                $option->setValue($this->encryptor->decrypt($option->getValue()));
            }
            $options[] = $option;
        }
        $entity->setOptions($options);

        return $entity;
    }
}
