<?php
namespace Aheadworks\Blog\Model\Import\Source;

use Aheadworks\Blog\Model\Import\Processor\Composite;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface as DataProcessorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

/**
 * Class AbstractSourceEntity
 */
class AbstractSourceEntity extends AbstractEntity
{
    /**
     * @var string
     */
    protected $masterAttributeCode;

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * @var array
     */
    protected $requiredColumnNames;

    /**
     * @var DataProcessorInterface
     */
    private $dataProcessor;

    /**
     * @var Composite
     */
    private $importProcessor;

    /**
     * AbstractSourceEntity constructor.
     * @param DataProcessorInterface $dataProcessor
     * @param Composite $importProcessor
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\ImportExport\Model\ImportFactory $importFactory
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param string $masterAttributeCode
     * @param string $entityCode
     * @param array $requiredColumnNames
     * @param array $data
     */
    public function __construct(
        DataProcessorInterface $dataProcessor,
        Composite $importProcessor,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        string $masterAttributeCode = '',
        string $entityCode = '',
        array $requiredColumnNames = [],
        array $data = []
    ) {
        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $data
        );
        $this->dataProcessor = $dataProcessor;
        $this->importProcessor = $importProcessor;
        $this->masterAttributeCode = $masterAttributeCode;
        $this->entityCode = $entityCode;
        $this->requiredColumnNames = $requiredColumnNames;
    }

    /**
     * @inheritDoc
     */
    protected function _importData()
    {
        $this->saveAndReplaceEntity();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getEntityTypeCode()
    {
        return $this->entityCode;
    }

    /**
     * @inheritDoc
     */
    public function validateRow(array $rowData, $rowNum)
    {
        foreach ($this->requiredColumnNames as $column) {
            $columnData = $rowData[$column] ?? null;
            if (!$columnData) {
                $errorCode = str_replace(' ', '', ucwords(str_replace('_', ' ', $column)));
                $errorMessage = sprintf('%s field is required', $column);
                $this->addRowError(
                    $errorCode . 'IsRequired',
                    $rowNum,
                    $column,
                    $errorMessage
                );
            }
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Save and replace entity
     */
    protected function saveAndReplaceEntity()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $rowData = $this->dataProcessor->process($rowData);
                $this->importProcessor->saveEntity($rowData, $this->getEntityTypeCode());
            }
        }
    }
}