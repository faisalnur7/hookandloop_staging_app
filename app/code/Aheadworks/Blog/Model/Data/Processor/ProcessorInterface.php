<?php
namespace Aheadworks\Blog\Model\Data\Processor;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Blog\Model\Data\Processor
 */
interface ProcessorInterface
{
    /**
     * Process data
     *
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function process($data);
}
