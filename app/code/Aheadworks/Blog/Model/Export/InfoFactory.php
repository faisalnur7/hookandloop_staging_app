<?php
namespace Aheadworks\Blog\Model\Export;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ImportExport\Model\Export\Adapter\Factory as AdapterFactory;
use Magento\ImportExport\Model\Export\ConfigInterface;
use Magento\ImportExport\Model\Export\Entity\Factory as EntityFactory;
use Psr\Log\LoggerInterface;

/**
 * Class InfoFactory
 */
class InfoFactory extends \Magento\ImportExport\Model\Export\Entity\ExportInfoFactory
{
    /**
     * @var ConfigInterface
     */
    private $exportConfig;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * InfoFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ConfigInterface $exportConfig
     * @param EntityFactory $entityFactory
     * @param AdapterFactory $exportAdapterFac
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigInterface $exportConfig,
        EntityFactory $entityFactory,
        AdapterFactory $exportAdapterFac,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->exportConfig = $exportConfig;
        $this->configProvider = $configProvider;
        parent::__construct($objectManager, $exportConfig, $entityFactory, $exportAdapterFac, $serializer, $logger);
    }

    /**
     * @param string $fileFormat
     * @param string $entity
     * @param string $exportFilter
     * @param array $skipAttr
     * @param string|null $locale
     * @return \Magento\ImportExport\Api\Data\ExportInfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($fileFormat, $entity, $exportFilter, $skipAttr = [], ?string $locale = null)
    {
        $this->exportConfig->merge($this->configProvider->getExportConfig());

        return parent::create($fileFormat, $entity, $exportFilter, $skipAttr, $locale);
    }
}
