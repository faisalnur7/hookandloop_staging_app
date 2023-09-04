<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\StorageFactory;
use Aheadworks\Helpdesk2\Model\Gateway\ParamExtractor;

/**
 * Class DefaultModel
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType
 */
class DefaultModel implements ConnectionInterface
{
    /**
     * @var StorageFactory
     */
    private $storageFactory;

    /**
     * @var ParamExtractor
     */
    private $paramExtractor;

    /**
     * @param StorageFactory $storageFactory
     * @param ParamExtractor $paramExtractor
     */
    public function __construct(
        StorageFactory $storageFactory,
        ParamExtractor $paramExtractor
    ) {
        $this->storageFactory = $storageFactory;
        $this->paramExtractor = $paramExtractor;
    }

    /**
     * @inheritdoc
     */
    public function getConnection(GatewayDataInterface $gateway): object
    {
        $params = $this->paramExtractor->extract($gateway);
        return $this->storageFactory->create($params);
    }
}
