<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Model\Export\ConfigProvider;
use Magento\ImportExport\Model\Export as ExportModel;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;

/**
 * Class Export
 */
class Export extends ExportModel
{
    /**
     * Export constructor.
     * @param LoggerInterface $logger
     * @param Filesystem $filesystem
     * @param ExportModel\ConfigInterface $exportConfig
     * @param ExportModel\Entity\Factory $entityFactory
     * @param ExportModel\Adapter\Factory $exportAdapterFac
     * @param ConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        LoggerInterface $logger,
        Filesystem $filesystem,
        ExportModel\ConfigInterface $exportConfig,
        ExportModel\Entity\Factory $entityFactory,
        ExportModel\Adapter\Factory $exportAdapterFac,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $exportConfig->merge($configProvider->getExportConfig());
        parent::__construct($logger, $filesystem, $exportConfig, $entityFactory, $exportAdapterFac, $data);
    }
}