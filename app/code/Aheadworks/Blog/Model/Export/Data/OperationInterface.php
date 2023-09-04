<?php
namespace Aheadworks\Blog\Model\Export\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Api\Data\ExtendedExportInfoInterface;

/**
 * Interface OperationInterface
 */
interface OperationInterface
{
    /**
     * Perform operation over entity data
     *
     * @param array $entityData
     * @return ExtendedExportInfoInterface
     * @throws LocalizedException
     */
    public function execute($entityData);
}