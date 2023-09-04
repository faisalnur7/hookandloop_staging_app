<?php


namespace VladimirPopov\WebForms\Model\Export;


use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Oauth\Exception;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Listing\Columns;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use VladimirPopov\WebForms\Api\Utility\ExportValueConverterInterface;
use VladimirPopov\WebForms\Model\Result;
use VladimirPopov\WebForms\Model\ResultRepository;

class ConvertResultGrid
{
    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var ResultRepository
     */
    protected $resultRepository;

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param ResultRepository $resultRepository
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        ResultRepository $resultRepository
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->resultRepository = $resultRepository;
    }

    /**
     * Get Customer name by result id
     * (Fix for customer column)
     *
     * @param $id string|int
     * @return string
     */
    protected function getCustomerName($id) {
        try {

            /** @var Result $result */
            $result = $this->resultRepository->getById($id);
            return $result->getCustomerName();
        } catch (\Exception $exception) {
            return '';
        }
    }

    /**
     * Replace newline characters with spaces
     * @param $arr array
     * @return array
     */
    protected function replaceNewlineCharacters($arr) {
        for($i = 0; $i < count($arr); ++$i) {
            if (is_string($arr[$i])) {
                $arr[$i] = str_replace(array("\r", "\n"), ' ', $arr[$i]);
            }
        }
        return $arr;
    }

    /**
     * Convert data in result
     *
     * @param UiComponentInterface $component
     * @param DocumentInterface|DataObject $item
     * @param array $fields
     * @throws \Exception
     */
    protected function convertData(UiComponentInterface $component, DocumentInterface $item, array $fields)
    {
        $columns = $this->getColumns($component);
        foreach ($fields as $fieldName) {
            $fieldValue = $item->getData($fieldName);

            if($fieldName == 'customer'){
                $fieldValue = $item->getData();
            }

            if (is_array($fieldValue)) {
                $item->setData($fieldName, json_encode($fieldValue));
            }

            if ($columns[$fieldName] instanceof ExportValueConverterInterface) {
                $item->setData($fieldName, $columns[$fieldName]->convertExportValue($fieldValue));
            }
        }
    }

    /**
     * Return grid columns
     *
     * @param UiComponentInterface $component
     * @return array
     * @throws \Exception
     */
    protected function getColumns(UiComponentInterface $component): array
    {
        $columns          = [];
        $columnsComponent = $this->getColumnsComponent($component);
        foreach ($columnsComponent->getChildComponents() as $column) {
            if ($column->getData('config/label') && $column->getData('config/dataType') !== 'actions') {
                $columns[$column->getName()] = $column;
            }
        }
        return $columns;
    }

    /**
     * Returns Columns component
     *
     * @param UiComponentInterface $component
     *
     * @return UiComponentInterface
     * @throws \Exception
     */
    protected function getColumnsComponent(UiComponentInterface $component): UiComponentInterface
    {
        foreach ($component->getChildComponents() as $childComponent) {
            if ($childComponent instanceof Columns) {
                return $childComponent;
            }
        }
        throw new Exception(__('No columns found'));
    }
}