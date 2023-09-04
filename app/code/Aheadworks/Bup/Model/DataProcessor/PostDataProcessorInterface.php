<?php
namespace Aheadworks\Bup\Model\DataProcessor;

/**
 * Interface PostDataProcessorInterface
 *
 * @package Aheadworks\Bup\Model\DataProcessor
 */
interface PostDataProcessorInterface
{
    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data);
}
