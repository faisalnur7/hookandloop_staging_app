<?php
namespace Aheadworks\Blog\Model\Rule\Product\Collection\Preparer;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface;
use Magento\Framework\Exception\ConfigurationMismatchException;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create preparer
     *
     * @param string $className
     * @param array $data
     * @return PreparerInterface
     * @throws ConfigurationMismatchException
     */
    public function create($className, array $data = [])
    {
        $preparer = $this->objectManager->create($className, $data);

        if ($preparer instanceof PreparerInterface) {
            return $preparer;
        }

        throw new ConfigurationMismatchException(
            __("Collection preparer %1 must implement %2", $className, PreparerInterface::class)
        );
    }
}
