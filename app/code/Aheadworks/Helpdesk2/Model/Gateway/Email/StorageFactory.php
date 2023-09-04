<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Laminas\Mail\Storage\AbstractStorage;
use Magento\Framework\ObjectManagerInterface;

class StorageFactory
{
    /**
     * @var array
     */
    private $storageList;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $storageList
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        array $storageList
    ) {
        $this->storageList = $storageList;
    }

    /**
     * Get mail storage
     *
     * @param array $params
     * @return AbstractStorage
     */
    public function create($params)
    {
        if (!array_key_exists($params['protocol'], $this->storageList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway protocol: %s', $params['protocol'])
            );
        }

        $protocol = $params['protocol'];
        return $this->objectManager->create(
            $this->storageList[$protocol],
            [
                'params' => $params
            ]
        );
    }

    /**
     * Create by protocol object
     *
     * @param string $protocolType
     * @param object $protocolObject
     * @return AbstractStorage
     */
    public function createByProtocolObject($protocolType, $protocolObject)
    {
        if (!array_key_exists($protocolType, $this->storageList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway protocol: %s', $protocolType)
            );
        }

        return $this->objectManager->create(
            $this->storageList[$protocolType],
            [
                'params' => $protocolObject
            ]
        );
    }
}
