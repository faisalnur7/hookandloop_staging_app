<?php
namespace Aheadworks\Helpdesk2\Model\Rejection\Validator;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ResultFactory
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Validator
 */
class ResultFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $instanceName;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = Result::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create Result class instance
     *
     * @return Result
     */
    public function create()
    {
        return $this->objectManager->create($this->instanceName);
    }
}

