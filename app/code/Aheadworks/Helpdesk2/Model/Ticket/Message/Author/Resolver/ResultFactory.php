<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ResultFactory
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver
 */
class ResultFactory
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
        $instanceName = \Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver\Result::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create result object
     *
     * @return \Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver\Result
     */
    public function create()
    {
        return $this->objectManager->create($this->instanceName);
    }
}
