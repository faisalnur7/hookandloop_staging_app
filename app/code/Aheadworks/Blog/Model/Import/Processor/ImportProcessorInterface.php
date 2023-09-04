<?php
namespace Aheadworks\Blog\Model\Import\Processor;

/**
 * Interface ImportInterfate
 */
interface ImportProcessorInterface
{
    /**
     * Run import
     *
     * @param array $data
     * @return bool
     */
    public function perform($data);
}