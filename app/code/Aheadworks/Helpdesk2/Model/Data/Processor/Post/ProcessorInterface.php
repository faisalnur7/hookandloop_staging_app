<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post
 */
interface ProcessorInterface
{
    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data);
}
