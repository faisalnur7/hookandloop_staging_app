<?php
namespace Aheadworks\Blog\Model\Import\Processor;

use Aheadworks\Blog\Model\Import\MessageManager;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Config;

/**
 * Class AbstractProcessor
 */
abstract class AbstractImport implements ImportProcessorInterface
{
    /**
     * @var Import
     */
    private $import;

    /**
     * @var Config
     */
    private $importConfig;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var array
     */
    private $configEntity;

    /**
     * AbstractProcessor constructor.
     * @param Import $import
     * @param Config $importConfig
     * @param MessageManager $messageManager
     * @param array $configEntity
     */
    public function __construct(
        Import $import,
        Config $importConfig,
        MessageManager $messageManager,
        array $configEntity = []
    ) {
        $this->import = $import;
        $this->importConfig = $importConfig;
        $this->messageManager = $messageManager;
        $this->configEntity = $configEntity;
    }

    /**
     * @inheritDoc
     */
    public function perform($data)
    {
        $result = false;

        $this->importConfig->merge($this->configEntity);
        $this->import->setData($data);

        $source = $this->import->uploadFileAndGetSource();
        $isValid = $this->import->validateSource($source);

        if ($isValid) {
            $result = $this->import->importSource();
        }

        $errorAgregator = $this->import->getErrorAggregator();
        $this->messageManager->addOperationResultMessages($errorAgregator, $this->import);

        return $result;
    }

    /**
     * @param $rowData
     * @param null|string $type
     * @return mixed
     */
    abstract public function saveEntity($rowData, $type = null);
}