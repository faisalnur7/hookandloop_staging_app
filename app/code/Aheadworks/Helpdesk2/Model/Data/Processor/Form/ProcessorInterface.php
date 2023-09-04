<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form
 */
interface ProcessorInterface
{
    /**
     * Prepare entity data for form
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data);

    /**
     * Prepare meta data for form
     *
     * @param array $meta
     * @return array
     */
    public function prepareMetaData($meta);
}
