<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor;

use Magento\GraphQl\Model\Query\ContextInterface;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor
 */
interface ProcessorInterface
{
    /**
     * Process data
     *
     * @param array $data
     * @param ContextInterface $context
     * @return array
     */
    public function process($data, $context);
}
