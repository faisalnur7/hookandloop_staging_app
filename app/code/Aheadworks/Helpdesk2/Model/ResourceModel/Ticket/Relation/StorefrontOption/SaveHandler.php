<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Relation\StorefrontOption;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly MetadataPool $metadataPool,
        private readonly ResourceConnection $resourceConnection,
        private readonly EncryptorInterface $encryptor
    ){}

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var TicketInterface $entity */
        $entityId = $entity->getEntityId();
        if (!$entityId || !is_array($entity->getOptions())) {
            return $entity;
        }
        $dataToInsert = [];
        /** @var TicketOptionInterface $option */
        foreach ($entity->getOptions() as $option) {
            $dataToInsert[] = [
                TicketOptionInterface::TICKET_ID => $entityId,
                TicketOptionInterface::ID => $option->getId(),
                TicketOptionInterface::LABEL => $option->getLabel(),
                TicketOptionInterface::VALUE => $this->encryptor->encrypt($option->getValue()),
                TicketOptionInterface::IS_ENCRYPTED => true
            ];
        }

        if ($dataToInsert) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(TicketInterface::class)->getEntityConnectionName()
            );
            $connection->insertMultiple(
                $this->resourceConnection->getTableName(TicketResourceModel::TICKET_OPTION_TABLE_NAME),
                $dataToInsert
            );
        }

        return $entity;
    }
}
