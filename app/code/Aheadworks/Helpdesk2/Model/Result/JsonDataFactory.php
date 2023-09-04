<?php
namespace Aheadworks\Helpdesk2\Model\Result;

use Magento\Framework\ObjectManagerInterface;

/**
 * JsonDataFactory class for @see \Aheadworks\Helpdesk2\Model\Result\JsonData
 */
class JsonDataFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var string
     */
    protected $instanceName = null;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = \Aheadworks\Helpdesk2\Model\Result\JsonData::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create json result object
     *
     * @return \Aheadworks\Helpdesk2\Model\Result\JsonData
     */
    public function create()
    {
        return $this->objectManager->create($this->instanceName);
    }
}
