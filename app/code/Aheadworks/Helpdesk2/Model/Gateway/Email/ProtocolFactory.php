<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol\AdapterInterface;
use Magento\Framework\ObjectManagerInterface;

class ProtocolFactory
{
    /**
     * @var array
     */
    private $protocolList;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $protocolList
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        array $protocolList
    ) {
        $this->protocolList = $protocolList;
    }

    /**
     * Get mail protocol
     *
     * @param array $params
     * @return AdapterInterface
     */
    public function create($params)
    {
        if (!array_key_exists($params['protocol'], $this->protocolList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway protocol: %s', $params['protocol'])
            );
        }

        $protocol = $params['protocol'];
        return $this->objectManager->create(
            $this->protocolList[$protocol],
            [
                'params' => $params
            ]
        );
    }
}
