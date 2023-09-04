<?php
namespace Aheadworks\Blog\Model\Export\Data\Operation;

use Aheadworks\Blog\Model\Export\Data\OperationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Api\Data\ExtendedExportInfoInterface;
use Aheadworks\Blog\Model\Export\InfoFactory as ExportInfoFactory;

/**
 * Class CreateExportInfo
 */
class CreateExportInfo implements OperationInterface
{
    /**
     * @var ExportInfoFactory
     */
    private $exportInfoFactory;

    /**
     * CreateExportInfo constructor.
     * @param ExportInfoFactory $exportInfoFactory
     */
    public function __construct(
        ExportInfoFactory $exportInfoFactory
    ) {
        $this->exportInfoFactory = $exportInfoFactory;
    }

    const FORMAT_FILE = 'csv';

    /**
     * @inheritDoc
     */
    public function execute($entityData)
    {
        /** @var ExportInfoFactory $dataObject */
        $dataObject = $this->exportInfoFactory->create(
            self::FORMAT_FILE,
            $entityData['entity'],
            $entityData['export_filter'],
            $entityData['skip_attr'] ?? []
        );

        return $dataObject;
    }
}