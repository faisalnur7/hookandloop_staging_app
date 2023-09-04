<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Message as RejectedMessageModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage as RejectedMessageResourceModel;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RejectedMessageInterface::ID;

    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ProcessorInterface $objectDataProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ProcessorInterface $objectDataProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->objectDataProcessor = $objectDataProcessor;
    }


    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(RejectedMessageModel::class, RejectedMessageResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        /** @var RejectedMessageModel $item */
        foreach ($this as $item) {
            $this->objectDataProcessor->prepareModelAfterLoad($item);
        }

        return $this;
    }
}
